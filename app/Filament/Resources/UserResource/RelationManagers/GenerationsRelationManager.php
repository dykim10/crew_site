<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Branch;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GenerationsRelationManager extends RelationManager
{
    protected static string $relationship = 'userGenerations';
    protected static ?string $title = '기수 참여 이력';

    public function form(Schema $schema): Schema
    {
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
                ->searchable()
                ->helperText('같은 기수에는 활동 지부 1곳만 기록됩니다.'),

            Select::make('branch_id')
                ->label('활동 지부')
                ->options(fn () => Branch::query()
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray())
                ->required()
                ->searchable()
                ->helperText('해당 기수에서의 지부. 현재 기수로 설정 시 users.branch_id도 갱신합니다.'),

            DatePicker::make('joined_at')
                ->label('합류일')
                ->native(false)
                ->displayFormat('Y년 m월 d일')
                ->placeholder('날짜를 선택하세요'),

            Toggle::make('is_current')
                ->label('현재 소속 기수')
                ->helperText('현재 활동 중인 기수로 표시됩니다.')
                ->inline(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['generation', 'branch']))
            ->columns([
                TextColumn::make('generation.number')
                    ->label('기수')
                    ->formatStateUsing(fn ($state) => $state.'기')
                    ->sortable(),

                TextColumn::make('generation.alias')
                    ->label('별칭')
                    ->placeholder('-'),

                TextColumn::make('branch.name')
                    ->label('활동 지부')
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
                    ->after(function (UserGeneration $record) {
                        if ($record->is_current) {
                            UserGeneration::where('user_id', $record->user_id)
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }
                        if ($record->branch_id && $record->is_current) {
                            $record->user?->update(['branch_id' => $record->branch_id]);
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
                            if ($record->branch_id) {
                                $record->user?->update(['branch_id' => $record->branch_id]);
                            }
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
