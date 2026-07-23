<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SearchEngineVisitResource\Pages;
use App\Models\SearchEngineVisit;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

/**
 * 搜索引擎访问记录资源管理
 * 展示各搜索引擎蜘蛛访问文章的明细列表，仅管理员可见
 */
class SearchEngineVisitResource extends Resource
{
    protected static ?string $model = SearchEngineVisit::class;

    protected static ?string $navigationIcon = 'heroicon-o-search';

    protected static ?string $navigationLabel = '搜索引擎访问记录';

    protected static ?string $pluralModelLabel = '搜索引擎访问记录';
    protected static ?string $modelLabel = '搜索引擎访问记录';

    protected static ?string $recordTitleAttribute = 'engine_name';

    public static function canViewAny(): bool
    {
        return auth()->user()->is_admin ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('post_id')
                    ->label('所属文章')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('engine_name')
                    ->label('搜索引擎')
                    ->required()
                    ->maxLength(50),

                Forms\Components\TextInput::make('ip_address')
                    ->label('IP 地址')
                    ->maxLength(45),

                Forms\Components\Textarea::make('user_agent')
                    ->label('User-Agent')
                    ->rows(3),

                Forms\Components\TextInput::make('page_url')
                    ->label('访问页面')
                    ->maxLength(500),

                Forms\Components\DateTimePicker::make('visited_at')
                    ->label('访问时间')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('engine_name')
                    ->label('搜索引擎')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('post.title')
                    ->label('文章')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->url(fn (SearchEngineVisit $record) => $record->post ? route('posts.show', $record->post->slug) : null, true)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP 地址')
                    ->sortable(),

                Tables\Columns\TextColumn::make('page_url')
                    ->label('访问页面')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('visited_at')
                    ->label('访问时间')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('visited_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('engine_name')
                    ->label('搜索引擎')
                    ->options(fn () => SearchEngineVisit::query()
                        ->select('engine_name')
                        ->distinct()
                        ->pluck('engine_name', 'engine_name')
                        ->toArray()
                    ),

                Tables\Filters\Filter::make('visited_at')
                    ->label('访问时间')
                    ->form([
                        Forms\Components\DatePicker::make('visited_from')->label('开始日期'),
                        Forms\Components\DatePicker::make('visited_until')->label('结束日期'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['visited_from'] ?? null, fn ($q) => $q->whereDate('visited_at', '>=', $data['visited_from']))
                            ->when($data['visited_until'] ?? null, fn ($q) => $q->whereDate('visited_at', '<=', $data['visited_until']));
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSearchEngineVisits::route('/'),
        ];
    }
}
