<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Models\EventRegistration;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';
    protected static ?string $title = '참가 신청자';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.nickname')
                    ->label('닉네임')
                    ->default('-'),

                TextColumn::make('created_at')
                    ->label('신청일')
                    ->date('Y.m.d H:i')
                    ->sortable(),
            ])
            ->actions([
                // 신청 내용 상세 보기 (복호화된 데이터 모달)
                Action::make('view_data')
                    ->label('내용 보기')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('신청 내용')
                    ->modalContent(function (EventRegistration $record) {
                        $event  = $record->event;
                        $schema = $event?->form_schema ?? [];
                        $data   = $record->getDecryptedFormData();

                        $rows = collect($schema)->map(function ($field) use ($data) {
                            $key   = $field['key'] ?? '';
                            $label = $field['data']['label'] ?? '항목';
                            $type  = $field['type'] ?? 'text';
                            $value = $data[$key] ?? null;

                            if ($value === null) return null;

                            $display = match(true) {
                                is_array($value)         => implode(', ', $value),
                                $type === 'image'        => '<img src="' . e($value) . '" style="max-width:300px;border-radius:8px">',
                                default                  => e($value),
                            };

                            return "<tr>
                                <td style='padding:8px 12px;font-weight:600;color:#555;white-space:nowrap;vertical-align:top'>{$label}</td>
                                <td style='padding:8px 12px;color:#222'>{$display}</td>
                            </tr>";
                        })->filter()->join('');

                        $html = "<table style='width:100%;border-collapse:collapse;font-size:0.85rem'>{$rows}</table>";

                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('닫기'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
}
