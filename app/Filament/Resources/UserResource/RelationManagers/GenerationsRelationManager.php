<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Generation;
use App\Models\UserGeneration;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GenerationsRelationManager extends RelationManager
{
    protected static string $relationship = 'userGenerations';
    protected static ?string $title = '기수 참여 이력';

    public function form(Schema $schema): Schema
    {
        $existingGenerationIds = UserGeneration::where('user_id', $this->getOwnerRecord()->id)
            ->pluck('generation_id')
            ->toArray();

        return $schema->components([
            Select::make('generation_id')
                ->label('기수')
                ->options(
                    Generation::orderByDesc('number')
                        ->get()
                        ->mapWithKeys(fn ($g) => [
                            $g->id => $g->alias
                                ? "{$g->number}기 — {$g->alias}"
                                : "{$g->number}기",
                        ])
                        ->toArray()
                )
                ->required()
                ->searchable(),

            DatePicker::make('joined_at')
                ->label('합류일')
                ->placeholder('기수 시작일'),

            Toggle::make('is_current')
                ->label('현재 소속 기수')
                ->helperText('현재 활동 중인 기수로 표시됩니다.')
                ->inline(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('generation.number')
                    ->label('기수')
                    ->formatStateUsing(fn ($state) => $state . '기')
                    ->sortable(),

                TextColumn::make('generation.alias')
                    ->label('별칭')
                    ->placeholder('-'),

                TextColumn::make('joined_at')
                    ->label('합류일')
                    ->date('Y.m.d')
                    ->placeholder('-'),

                IconColumn::make('is_current')
                    ->label('현재 소속')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('기수 추가')
                    ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin']))
                    // is_current를 true로 설정하면 기존 current를 해제
                    ->after(function (UserGeneration $record) {
                        if ($record->is_current) {
                            UserGeneration::where('user_id', $record->user_id)
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('수정')
                    ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin']))
                    ->after(function (UserGeneration $record) {
                        if ($record->is_current) {
                            UserGeneration::where('user_id', $record->user_id)
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }
                    }),

                DeleteAction::make()
                    ->label('삭제')
                    ->visible(fn () => auth()->user()->role === 'super_admin'),
            ])
            ->defaultSort('generation_id', 'desc')
            ->striped();
    }
}
