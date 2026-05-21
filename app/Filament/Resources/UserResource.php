<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = '구성원 관리';
    protected static ?string $modelLabel = '구성원';
    protected static ?string $pluralModelLabel = '구성원 목록';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '회원';
    }

    /**
     * 관리자가 직접 계정을 생성하는 시나리오는 없으므로 폼은 수정 전용 필드만 노출.
     * email·password 는 노출하지 않고, invite_code 는 히스토리 확인용으로 disabled 처리.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nickname')
                ->label('닉네임')
                ->required()
                ->maxLength(50),

            Select::make('role')
                ->label('권한')
                ->options([
                    'super_admin'  => '슈퍼관리자',
                    'region_admin' => '지역관리자',
                    'operator'     => '운영자',
                    'member'       => '일반멤버',
                ])
                ->required(),

            Toggle::make('is_beta')
                ->label('베타 사용자'),

            TextInput::make('invite_code')
                ->label('초대 코드')
                ->maxLength(20)
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nickname')
                    ->label('닉네임')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('권한')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin'  => 'danger',
                        'region_admin' => 'warning',
                        'operator'     => 'info',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin'  => '슈퍼관리자',
                        'region_admin' => '지역관리자',
                        'operator'     => '운영자',
                        default        => '일반멤버',
                    }),

                IconColumn::make('is_beta')
                    ->label('베타')
                    ->boolean(),

                TextColumn::make('last_login_at')
                    ->label('최근 로그인')
                    ->dateTime('Y.m.d H:i')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('가입일')
                    ->date('Y.m.d')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('권한')
                    ->options([
                        'super_admin'  => '슈퍼관리자',
                        'region_admin' => '지역관리자',
                        'operator'     => '운영자',
                        'member'       => '일반멤버',
                    ]),
            ])
            ->actions([
                EditAction::make()->label('수정'),
            ])
            ->bulkActions([
                BulkActionGroup::make([]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
