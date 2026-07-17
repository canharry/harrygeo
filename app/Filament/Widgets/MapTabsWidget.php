<?php

namespace App\Filament\Widgets;

use App\Models\Visit;
use Filament\Widgets\Widget;

/**
 * 访问量地图标签页小部件
 * 将世界地图与中国地图整合在一个小部件内，通过标签页切换展示
 */
class MapTabsWidget extends Widget
{
    /**
     * 小部件视图路径
     */
    protected static string $view = 'filament.widgets.map-tabs';

    /**
     * 小部件排序
     */
    protected static ?int $sort = 4;

    /**
     * 登录用户可见
     */
    public static function canView(): bool
    {
        return auth()->check();
    }

    /**
     * 默认占满全部列宽
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * 小部件标题
     */
    protected static ?string $heading = null;

    /**
     * 向视图注入数据，普通用户仅看自己文章读者的分布
     */
    protected function getViewData(): array
    {
        $visitQuery = Visit::query();

        if (!auth()->user()->is_admin) {
            $visitQuery->whereHas('post', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        $countryData = (clone $visitQuery)
            ->selectRaw('country_code, COUNT(*) as total')
            ->groupBy('country_code')
            ->pluck('total', 'country_code')
            ->toArray();

        $regionData = (clone $visitQuery)
            ->selectRaw('region_code, COUNT(*) as total')
            ->where('country_code', 'CN')
            ->whereNotNull('region_code')
            ->groupBy('region_code')
            ->pluck('total', 'region_code')
            ->toArray();

        return [
            'countryData' => $countryData,
            'regionData'  => $regionData,
            'worldTotal'  => array_sum($countryData),
            'chinaTotal'  => array_sum($regionData),
        ];
    }
}
