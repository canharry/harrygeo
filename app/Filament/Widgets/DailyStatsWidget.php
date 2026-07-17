<?php

namespace App\Filament\Widgets;

use App\Models\VisitSummary;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

/**
 * 每日访问统计小部件
 * 展示今日浏览量、文章阅读量与点赞量
 */
class DailyStatsWidget extends BaseWidget
{
    /**
     * 小部件排序
     */
    protected static ?int $sort = 1;

    /**
     * 登录用户可见
     */
    public static function canView(): bool
    {
        return auth()->check();
    }

    /**
     * 统计卡片默认占满三列
     */
    protected function getColumns(): int
    {
        return 3;
    }

    /**
     * 构建统计卡片
     */
    protected function getCards(): array
    {
        $user = auth()->user();
        $today = now()->toDateString();

        if ($user->is_admin) {
            $todaySummary = VisitSummary::where('summary_date', $today)->first();
            $pageViews = $todaySummary?->page_views ?? 0;
            $postReads = $todaySummary?->post_reads ?? 0;
            $likesCount = $todaySummary?->likes_count ?? 0;
        } else {
            $postIds = $user->posts()->pluck('id');

            $pageViews = \App\Models\Visit::whereDate('visited_at', $today)
                ->whereIn('post_id', $postIds)
                ->count();

            $postReads = \App\Models\Visit::whereDate('visited_at', $today)
                ->whereIn('post_id', $postIds)
                ->whereNotNull('post_id')
                ->count();

            $likesCount = \App\Models\PostLike::whereDate('liked_at', $today)
                ->whereIn('post_id', $postIds)
                ->count();
        }

        return [
            Card::make(__('filament.widgets.daily_page_views'), $pageViews)
                ->description(__('filament.widgets.daily_page_views_desc'))
                ->icon('heroicon-o-eye')
                ->color('primary'),

            Card::make(__('filament.widgets.daily_post_reads'), $postReads)
                ->description(__('filament.widgets.daily_post_reads_desc'))
                ->icon('heroicon-o-document-text')
                ->color('success'),

            Card::make(__('filament.widgets.daily_likes'), $likesCount)
                ->description(__('filament.widgets.daily_likes_desc'))
                ->icon('heroicon-o-heart')
                ->color('danger'),
        ];
    }
}
