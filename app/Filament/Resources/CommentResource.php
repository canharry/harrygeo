<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

/**
 * 评论资源管理
 * 提供文章评论在 Filament 后台的审核与管理界面
 */
class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-alt-2';

    protected static ?string $navigationLabel = '评论';

    protected static ?string $pluralModelLabel = '评论';
    protected static ?string $modelLabel = '评论';

    protected static ?string $recordTitleAttribute = 'content';

    public static function canViewAny(): bool
    {
        return auth()->user()->is_admin ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // 所属文章
                        Forms\Components\Select::make('post_id')
                            ->label('所属文章')
                            ->relationship('post', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),

                        // 评论者昵称
                        Forms\Components\TextInput::make('author_name')
                            ->label('评论者')
                            ->required()
                            ->maxLength(255),

                        // 评论者邮箱
                        Forms\Components\TextInput::make('author_email')
                            ->label('邮箱')
                            ->email()
                            ->maxLength(255),

                        // 评论内容
                        Forms\Components\Textarea::make('content')
                            ->label('评论内容')
                            ->required()
                            ->rows(5)
                            ->maxLength(2000),

                        // 审核状态
                        Forms\Components\Toggle::make('is_approved')
                            ->label('是否通过审核')
                            ->default(false)
                            ->inline(false),

                        // 父级评论
                        Forms\Components\Select::make('parent_id')
                            ->label('父级评论')
                            ->relationship('parent', 'content')
                            ->searchable()
                            ->preload()
                            ->placeholder('一级评论无需选择'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.title')
                    ->label('文章')
                    ->limit(30)
                    ->sortable(),

                Tables\Columns\TextColumn::make('author_name')
                    ->label('评论者')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('content')
                    ->label('内容')
                    ->limit(60),

                Tables\Columns\BooleanColumn::make('is_approved')
                    ->label('已审核')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('评论时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('审核状态')
                    ->placeholder('全部')
                    ->trueLabel('已通过')
                    ->falseLabel('待审核'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
