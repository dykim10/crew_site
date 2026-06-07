<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoardResource\Pages;
use App\Models\Board;
use App\Models\BoardComment;
use App\Services\AdminLogService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class BoardResource extends Resource
{
    protected static ?string $model = Board::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = '게시판 관리';
    protected static ?string $modelLabel = '게시글';
    protected static ?string $pluralModelLabel = '게시글 목록';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return '게시판 관리';
    }

    public static function canCreate(): bool { return false; }

    public static function canDelete(Model $record): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'region_admin']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withTrashed()
            ->with(['author']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make('게시글 정보')
                ->schema([
                    Placeholder::make('board_type_label')
                        ->label('게시판')
                        ->content(fn ($record) => match($record?->board_type) {
                            'free' => '자유게시판',
                            'qna'  => '문의게시판',
                            default => $record?->board_type ?? '—',
                        }),

                    Placeholder::make('author_name')
                        ->label('작성자')
                        ->content(fn ($record) => $record?->author?->name ?? '(탈퇴)'),

                    Placeholder::make('view_count_label')
                        ->label('조회수')
                        ->content(fn ($record) => number_format($record?->view_count ?? 0) . '회'),

                    Placeholder::make('created_label')
                        ->label('작성일')
                        ->content(fn ($record) => $record?->created_at?->format('Y.m.d H:i') ?? '—'),
                ])
                ->columns(4),

            Section::make('제목 / 내용')
                ->schema([
                    Placeholder::make('title_label')
                        ->label('제목')
                        ->content(fn ($record) => $record?->title ?? '—'),

                    Placeholder::make('content_label')
                        ->label('내용')
                        ->content(fn ($record) => new HtmlString(
                            '<div class="prose max-w-none text-sm leading-relaxed">'
                            . nl2br(e($record?->content ?? ''))
                            . '</div>'
                        )),
                ]),

            Section::make('댓글 / 답변')
                ->schema([
                    Placeholder::make('comments_list')
                        ->label('')
                        ->content(function ($record) {
                            if (!$record) return '—';
                            $comments = BoardComment::where('board_id', $record->id)
                                ->whereNull('parent_id')
                                ->whereNull('deleted_at')
                                ->with(['author', 'replies.author'])
                                ->orderBy('created_at')
                                ->get();

                            if ($comments->isEmpty()) {
                                return new HtmlString('<p class="text-gray-400 text-sm">댓글이 없습니다.</p>');
                            }

                            $html = '<div class="space-y-3">';
                            foreach ($comments as $comment) {
                                $author = e($comment->author?->name ?? '탈퇴');
                                $date   = $comment->created_at?->format('Y.m.d H:i');
                                $body   = nl2br(e($comment->content));
                                $html  .= "<div class='rounded-md border border-gray-200 p-3 bg-gray-50'>"
                                        . "<div class='text-xs text-gray-500 mb-1 flex gap-2'><span class='font-semibold text-gray-700'>{$author}</span><span>{$date}</span></div>"
                                        . "<div class='text-sm text-gray-800'>{$body}</div>";

                                foreach ($comment->replies as $reply) {
                                    $rAuthor = e($reply->author?->name ?? '탈퇴');
                                    $rDate   = $reply->created_at?->format('Y.m.d H:i');
                                    $rBody   = nl2br(e($reply->content));
                                    $html   .= "<div class='ml-4 mt-2 rounded border border-gray-100 bg-white p-2'>"
                                             . "<div class='text-xs text-gray-400 mb-1 flex gap-2'><span class='font-semibold'>{$rAuthor}</span><span>{$rDate}</span></div>"
                                             . "<div class='text-sm text-gray-700'>{$rBody}</div>"
                                             . "</div>";
                                }
                                $html .= '</div>';
                            }
                            $html .= '</div>';
                            return new HtmlString($html);
                        }),
                ]),

            Section::make('관리자 답변 등록')
                ->description('문의게시판 전용. 관리자 계정으로 댓글을 등록합니다.')
                ->schema([
                    Textarea::make('admin_reply')
                        ->label('답변 내용')
                        ->rows(4)
                        ->placeholder('답변을 입력하세요.')
                        ->helperText('저장 시 관리자 계정으로 댓글이 등록됩니다.'),
                ])
                ->visible(fn ($record) => $record?->board_type === 'qna'),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('board_type')
                    ->label('게시판')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'free' => 'info',
                        'qna'  => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'free' => '자유',
                        'qna'  => '문의',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('title')
                    ->label('제목')
                    ->limit(40)
                    ->searchable()
                    ->description(fn (Board $record) => $record->deleted_at
                        ? '🗑 삭제된 게시글'
                        : ($record->is_secret ? '🔒 비밀글' : null)),

                TextColumn::make('author.name')
                    ->label('작성자')
                    ->default('(탈퇴)')
                    ->searchable(),

                TextColumn::make('view_count')
                    ->label('조회')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('comments_count')
                    ->label('댓글')
                    ->counts('comments')
                    ->alignCenter(),

                IconColumn::make('is_secret')
                    ->label('비밀')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('')
                    ->trueColor('warning'),

                TextColumn::make('created_at')
                    ->label('작성일')
                    ->date('Y.m.d')
                    ->sortable(),

                TextColumn::make('deleted_at')
                    ->label('삭제일')
                    ->date('Y.m.d')
                    ->sortable()
                    ->color('danger')
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('board_type')
                    ->label('게시판')
                    ->options([
                        'free' => '자유게시판',
                        'qna'  => '문의게시판',
                    ]),

                TernaryFilter::make('deleted_at')
                    ->label('삭제 여부')
                    ->nullable()
                    ->trueLabel('삭제된 글만')
                    ->falseLabel('삭제 안된 글만')
                    ->queries(
                        true:  fn (Builder $q) => $q->whereNotNull('deleted_at'),
                        false: fn (Builder $q) => $q->whereNull('deleted_at'),
                        blank: fn (Builder $q) => $q,
                    ),

                TernaryFilter::make('is_secret')
                    ->label('비밀글')
                    ->trueLabel('비밀글만')
                    ->falseLabel('공개글만'),
            ])
            ->actions([
                EditAction::make()->label('상세/답변'),

                Action::make('restore')
                    ->label('복구')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Board $record) {
                        $record->restore();
                        AdminLogService::log('restored', 'Board', $record->id, "게시글 #{$record->id} 복구");
                        Notification::make()->success()->title('복구되었습니다.')->send();
                    })
                    ->visible(fn (Board $record) => $record->deleted_at !== null),

                DeleteAction::make()
                    ->label('삭제')
                    ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => in_array(auth()->user()->role, ['super_admin', 'region_admin'])),
                ]),
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
            'index' => Pages\ListBoards::route('/'),
            'edit'  => Pages\EditBoard::route('/{record}/edit'),
        ];
    }
}
