<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use App\Models\EventScore;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScoresRelationManager extends RelationManager
{
    protected static string $relationship = 'scores';
    protected static ?string $title = '이벤트 점수';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('구성원')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('score')
                ->label('점수 / 달성 km')
                ->numeric()
                ->required(),

            TextInput::make('memo')
                ->label('메모')
                ->maxLength(200),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('user.name')
                    ->label('구성원')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('score')
                    ->label('점수 / km')
                    ->numeric(decimalPlaces: 1)
                    ->sortable(),

                TextColumn::make('source')
                    ->label('출처')
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'auto' ? 'success' : 'warning')
                    ->formatStateUsing(fn (?string $state): string => $state === 'auto' ? '자동' : '수동'),

                TextColumn::make('memo')
                    ->label('메모')
                    ->limit(30),

                TextColumn::make('created_at')
                    ->label('부여일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->defaultSort('score', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('수동 점수 입력')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['source']     = 'manual';
                        $data['awarded_by'] = auth()->id();
                        return $data;
                    })
                    ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin'])),
            ])
            ->actions([
                EditAction::make()->label('수정')
                    ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin'])),
                DeleteAction::make()->label('삭제')
                    ->visible(fn () => auth()->user()->role === 'super_admin'),
            ])
            ->striped();
    }
}
