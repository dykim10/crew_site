<?php

namespace App\Filament\Resources\BoardResource\Pages;

use App\Filament\Resources\BoardResource;
use App\Models\BoardComment;
use App\Services\AdminLogService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBoard extends EditRecord
{
    protected static string $resource = BoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin'])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /** 저장 시 admin_reply 필드가 있으면 댓글로 등록 후 Board 저장에서 제외 */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['admin_reply']) && $this->record->board_type === 'qna') {
            BoardComment::create([
                'board_id'  => $this->record->id,
                'user_id'   => auth()->id(),
                'content'   => trim($data['admin_reply']),
                'parent_id' => null,
            ]);
            AdminLogService::log('replied', 'Board', $this->record->id,
                "게시글 #{$this->record->id} 관리자 답변 등록");

            Notification::make()->success()->title('답변이 등록되었습니다.')->send();
        }

        unset($data['admin_reply']);
        return $data;
    }
}
