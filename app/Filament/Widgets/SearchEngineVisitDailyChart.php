<?php

namespace App\Filament\Widgets;

use App\Models\SearchEngineVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * 搜索引擎访问每日趋势图
 */
class SearchEngineVisitDailyChart extends ChartWidget
{
    protected static ?string $heading = '搜索引擎访问每日趋势';

    protected static ?int $sort = 1;

    protected static ?string $maxHeight = '250px';

    public static function canView(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();

        $rows = SearchEngineVisit::query()
            ->select(DB::raw('DATE(visited_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('visited_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $labels = [];
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('m-d');
            $data[] = $rows[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label'           => '搜索引擎访问次数',
                    'data'            => $data,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill'            => true,
                    'tension'         => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
