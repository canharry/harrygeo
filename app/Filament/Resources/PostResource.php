<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\RichEditor;
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

    public static function getEloquentQuery():Builder
    {
        $query = parent::getEloquentQuery()->with(['aiReferences', 'user', 'tags']);

        // 非管理员只能查看自己的文章
        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return auth()->check();
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->is_admin || $record->user_id === auth()->id();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->is_admin || $record->user_id === auth()->id();
    }

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
                            ->unique(
                                ignoreRecord: true,
                                callback: function ($rule, ?Post $record) {
                                    return $rule->where('user_id', $record?->user_id ?? auth()->id());
                                },
                            )
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),

                        // URL 别名
                        Forms\Components\TextInput::make('slug')
                            ->label('URL 别名')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('留空时将根据标题自动生成，建议只包含英文、数字和横线'),

                        // 所属分类
                        Forms\Components\Select::make('category_id')
                            ->label('分类')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('没有找到需要的分类？点击右侧 “+” 号新建分类。')
                            ->createOptionModalHeading('新建分类')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('分类名称')
                                    ->required()
                                    ->unique('categories', 'name')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL 别名')
                                    ->required()
                                    ->unique('categories', 'slug'),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('分类颜色')
                                    ->default('#667eea'),
                                Forms\Components\TextInput::make('icon')
                                    ->label('图标类名')
                                    ->default('bi-folder')
                                    ->helperText('Bootstrap Icons 类名，例如 bi-folder'),
                                Forms\Components\Textarea::make('description')
                                    ->label('分类描述')
                                    ->rows(2)
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $category = \App\Models\Category::create([
                                    'name'        => $data['name'],
                                    'slug'        => $data['slug'] ?: \Illuminate\Support\Str::slug($data['name']),
                                    'color'       => $data['color'] ?? '#667eea',
                                    'icon'        => $data['icon'] ?? 'bi-folder',
                                    'description' => $data['description'] ?? null,
                                    'sort_order'  => 0,
                                    'is_show'     => true,
                                ]);

                                return $category->id;
                            }),

                        // 标签多选
                        Forms\Components\MultiSelect::make('tags')
                            ->label('标签')
                            ->relationship('tags', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('没有找到需要的标签？点击右侧 “+” 号新建标签。')
                            ->createOptionModalHeading('新建标签')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('标签名称')
                                    ->required()
                                    ->unique('tags', 'name')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL 别名')
                                    ->required()
                                    ->unique('tags', 'slug'),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('标签颜色')
                                    ->default('#ff7eb3'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $tag = \App\Models\Tag::create([
                                    'name'  => $data['name'],
                                    'slug'  => $data['slug'] ?: \Illuminate\Support\Str::slug($data['name']),
                                    'color' => $data['color'] ?? '#ff7eb3',
                                ]);

                                return $tag->id;
                            }),

                        // 封面图上传
                        Forms\Components\FileUpload::make('cover_image_file')
                            ->label('上传封面图')
                            ->image()
                            ->directory('covers')
                            ->maxSize(2048)
                            ->helperText('支持 jpg、png、gif，最大 2MB；上传后将优先使用本地上传'),

                        // 封面图地址
                        Forms\Components\TextInput::make('cover_image')
                            ->label('或填写封面图地址')
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
                        RichEditor::make('content')
                            ->label('正文')
                            ->required()
                            ->columnSpan('full')
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                                'videoUpload',
                                'videoLink',
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 700px; max-height: 1800px; overflow-y: auto; resize: vertical;']),

                        // 视频上传
                        Forms\Components\FileUpload::make('video_file')
                            ->label('上传视频')
                            ->directory('videos')
                            ->maxSize(51200)
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'])
                            ->helperText('支持 mp4、webm、ogg、mov，最大 50MB；上传后将优先使用本地上传；该视频将会在文章的最上方展示'),

                        // 视频链接（外部平台）
                        Forms\Components\TextInput::make('video_url')
                            ->label('或填写视频链接')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com/video.mp4 或 YouTube/Bilibili 链接')
                            ->helperText('支持 YouTube、Bilibili 等外部视频链接；与上传同时存在时优先使用本地上传；该视频将会在文章的最上方展示'),

                        // 原创 / 转载标记
                        Forms\Components\Select::make('is_original')
                            ->label('文章类型')
                            ->options([
                                '1' => '原创',
                                '0' => '转载',
                            ])
                            ->default('1')
                            ->required()
                            ->reactive()
                            ->afterStateHydrated(function (callable $set, $state) {
                                $set('is_original', $state ? '1' : '0');
                            })
                            ->helperText('选择“转载”时必须填写原文链接'),

                        // 转载来源链接
                        Forms\Components\TextInput::make('original_url')
                            ->label('原文链接')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://example.com/original-article')
                            ->helperText('转载文章请填写原文地址')
                            ->visible(fn (callable $get) => $get('is_original') === '0')
                            ->required(fn (callable $get) => $get('is_original') === '0'),

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

                        // 浏览量（仅管理员可见）
                        Forms\Components\TextInput::make('views')
                            ->label('浏览量')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->hidden(fn () => ! auth()->user()->is_admin),

                        // 点赞数（仅管理员可见）
                        Forms\Components\TextInput::make('likes')
                            ->label('点赞数')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->hidden(fn () => ! auth()->user()->is_admin),

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
                    ->limit(25)
                    ->wrap(),


                Tables\Columns\TextColumn::make('category.name')
                    ->label('分类')
                    ->sortable(),

                Tables\Columns\TagsColumn::make('tags.name')
                    ->label('标签')
                    ->separator(',')
                    ->limit(2),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('归属人')
                    ->hidden(fn () => ! auth()->user()->is_admin)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BooleanColumn::make('is_published')
                    ->label('已发布')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_original')
                    ->label('原创')
                    ->boolean()
                    ->trueIcon('heroicon-o-pencil')
                    ->falseIcon('heroicon-o-share')
                    ->trueColor('success')
                    ->falseColor('warning')
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

                Tables\Columns\TextColumn::make('aiReferences_summary')
                    ->label('AI 收录')
                    ->formatStateUsing(function ($record) {
                        $refs = $record->aiReferences;

                        if ($refs->isEmpty()) {
                            return '—';
                        }

                        return $refs->map(function ($ref) {
                            return "{$ref->name}: {$ref->count}";
                        })->implode(', ');
                    })
                    ->wrap()
                    ->tooltip(function ($record) {
                        $refs = $record->aiReferences;

                        if ($refs->isEmpty()) {
                            return '暂无 AI 收录记录';
                        }

                        return $refs->map(function ($ref) {
                            return "{$ref->name} 收录 {$ref->count} 次";
                        })->implode("\n");
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d')
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
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
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
