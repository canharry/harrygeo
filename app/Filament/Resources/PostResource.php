<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * 文章资源管理
 * 提供文章在 Filament 后台的增删改查界面
 */
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    // 后台侧边栏图标
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // 导航标签中文
    protected static ?string $navigationLabel = '文章';

    // 页面标题中文
    protected static ?string $pluralModelLabel = '文章';
    protected static ?string $modelLabel = '文章';

    // 排序：按创建时间倒序
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        // 文章标题
                        Forms\Components\TextInput::make('title')
                            ->label('标题')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),

                        // URL 别名
                        Forms\Components\TextInput::make('slug')
                            ->label('URL 别名')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('用于生成文章详情页链接，建议只包含英文、数字和横线'),

                        // 所属分类
                        Forms\Components\Select::make('category_id')
                            ->label('分类')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        // 标签多选
                        Forms\Components\MultiSelect::make('tags')
                            ->label('标签')
                            ->relationship('tags', 'name')
                            ->searchable()
                            ->preload(),

                        // 封面图地址
                        Forms\Components\TextInput::make('cover_image')
                            ->label('封面图地址')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com/image.jpg')
                            ->helperText('留空则使用默认渐变占位图'),

                        // 文章摘要
                        Forms\Components\Textarea::make('excerpt')
                            ->label('摘要')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('留空时自动从正文截取前 150 字'),

                        // 正文内容
                        Forms\Components\RichEditor::make('content')
                            ->label('正文')
                            ->required()
                            ->columnSpan('full'),

                        // 发布状态
                        Forms\Components\Toggle::make('is_published')
                            ->label('是否发布')
                            ->default(false)
                            ->inline(false),

                        // 置顶推荐
                        Forms\Components\Toggle::make('is_featured')
                            ->label('是否推荐')
                            ->default(false)
                            ->inline(false),

                        // 浏览量
                        Forms\Components\TextInput::make('views')
                            ->label('浏览量')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        // 点赞数
                        Forms\Components\TextInput::make('likes')
                            ->label('点赞数')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        // 发布时间
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('发布时间')
                            ->default(now()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('分类')
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_published')
                    ->label('已发布')
                    ->sortable(),

                Tables\Columns\BooleanColumn::make('is_featured')
                    ->label('推荐')
                    ->sortable(),

                Tables\Columns\TextColumn::make('views')
                    ->label('浏览')
                    ->sortable(),

                Tables\Columns\TextColumn::make('likes')
                    ->label('点赞')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('分类')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('发布状态')
                    ->placeholder('全部')
                    ->trueLabel('已发布')
                    ->falseLabel('草稿'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('推荐状态')
                    ->placeholder('全部')
                    ->trueLabel('已推荐')
                    ->falseLabel('未推荐'),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
