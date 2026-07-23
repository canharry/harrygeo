<?php

namespace App\Filament\Widgets;

use App\Models\AiVisit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

/**
 * AI 平台分布统计图
 */
class AiVisitPlatformChart extends ChartWidget
{
    protected static ?string $heading = 'AI 平台分布';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '250px';

    public static function canView(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    protected function getData(): array
    {
        $rows = AiVisit::query()
            ->select('ai_name as name', DB::raw('COUNT(*) as count'))
            ->groupBy('ai_name')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'name')
            ->toArray();

        $colors = [
            '#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe',
            '#00f2fe', '#43e97b', '#fa709a', '#fee140', '#30cfd0',
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
