<?php

namespace App\Filament\Resources\ApplicationFormResource\Pages;

use App\Filament\Resources\ApplicationFormResource;
use App\Models\GoogleForm;
use App\Services\ApplicationFormBranchService;
use App\Services\ApplicationFormSheetImportService;
use App\Services\GenerationVisibilityService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateApplicationForm extends CreateRecord
{
    protected static string $resource = ApplicationFormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['form_fields'] = app(ApplicationFormSheetImportService::class)
            ->ensureFieldKeys($data['form_fields'] ?? []);
        $data['images'] = app(ApplicationFormBranchService::class)
            ->normalizeImages($data['images'] ?? []);
        $data['branch_settings'] = array_values($data['branch_settings'] ?? []);

        return $data;
    }

    protected function afterCreate(): void
    {
        GenerationVisibilityService::forgetCache();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_google_sheet_fields')
                ->label('구글 시트 항목 가져오기')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->modalHeading('구글 응답 시트 → 추가 항목')
                ->modalDescription('연결된 기수 모집 구글 폼의 시트 헤더를 읽어, 이름·연락처·이메일·타임스탬프를 제외한 문항을 폼에 추가합니다.')
                ->modalSubmitActionLabel('가져오기')
                ->form([
                    Select::make('google_form_id')
                        ->label('구글 폼')
                        ->options(fn () => GoogleForm::query()
                            ->where('purpose', GoogleForm::PURPOSE_GENERATION_RECRUIT)
                            ->where('is_active', true)
                            ->orderByDesc('id')
                            ->get()
                            ->mapWithKeys(fn (GoogleForm $g) => [
                                $g->id => $g->title.($g->generation_id ? " (#{$g->generation_id})" : ''),
                            ])
                            ->toArray())
                        ->searchable()
                        ->required()
                        ->helperText('Sheet ID가 등록된 기수 모집 구글 폼만 선택합니다.'),
                ])
                ->action(function (array $data): void {
                    $googleForm = GoogleForm::find($data['google_form_id'] ?? null);
                    if (! $googleForm) {
                        Notification::make()->danger()->title('구글 폼을 찾을 수 없습니다.')->send();

                        return;
                    }

                    try {
                        $result = app(ApplicationFormSheetImportService::class)
                            ->mergeFromGoogleForm($googleForm, $this->data['form_fields'] ?? []);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->danger()
                            ->title('가져오기 실패')
                            ->body($e->getMessage())
                            ->persistent()
                            ->send();

                        return;
                    }

                    $this->data['form_fields'] = $result['fields'];
                    $this->form->fill($this->data);

                    $mapping = $result['mapping'];
                    $googleForm->update([
                        'column_mapping' => array_merge(
                            $googleForm->column_mapping ?? [],
                            array_filter([
                                'name'  => $mapping['name'],
                                'email' => $mapping['email'],
                                'phone' => $mapping['phone'],
                            ], fn ($v) => filled($v))
                        ),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('시트 항목 반영')
                        ->body('추가 '.count($result['added']).'건. 저장을 눌러 폼을 생성하세요.')
                        ->send();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
