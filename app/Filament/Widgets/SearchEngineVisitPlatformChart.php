<?php

namespace App\Filament\Widgets;

use App\Models\SearchEngineVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * 搜索引擎平台分布统计图
 */
class SearchEngineVisitPlatformChart extends ChartWidget
{
    protected static ?string $heading = '搜索引擎平台分布';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '250px';

    public static function canView(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    protected function getData(): array
    {
        $rows = SearchEngineVisit::query()
            ->select('engine_name as name', DB::raw('COUNT(*) as count'))
            ->groupBy('engine_name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'name')
            ->toArray();

        $colors = [
            '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6',
            '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1',
        ];

        return [
            'datasets' => [
                [
                    'data'            => array_values($rows),
                    'backgroundColor' => array_slice($colors, 0, count($rows)),
                    'borderWidth'     => 0,
                ],
            ],
            'labels' => array_keys($rows),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): ?array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
