<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * 文章阅读排行榜小部件
 * 展示阅读量最高的前 10 篇文章
 */
class PostReadLeaderboardWidget extends BaseWidget
{
    /**
     * 小部件排序
     */
    protected static ?int $sort = 2;

    /**
     * 登录用户可见
     */
    public static function canView(): bool
    {
        return auth()->check();
    }

    /**
     * 表格默认占满两列
     */
    protected int | string | array $columnSpan = 2;

    /**
     * 表格标题
     */
    protected function getTableHeading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return __('filament.widgets.read_leaderboard_title');
    }

    /**
     * 表格查询：按浏览量倒序取前 10，普通用户仅看自己文章
     */
    protected function getTableQuery(): Builder
    {
        $query = Post::query()->where('is_published', true);

        if (!auth()->user()->is_admin) {
            $query->where('user_id', auth()->id());
        }

        return $query->orderByDesc('views')->limit(10);
    }

    /**
     * 表格列定义
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->label(__('filament.widgets.post_title'))
                ->limit(40)
                ->searchable(),

            Tables\Columns\TextColumn::make('views')
                ->label(__('filament.widgets.views_count'))
                ->sortable(),

            Tables\Columns\TextColumn::make('category.name')
                ->label(__('filament.widgets.category')),

            Tables\Columns\TextColumn::make('published_at')
                ->label(__('filament.widgets.published_at'))
                ->date('Y-m-d'),
        ];
    }
}
