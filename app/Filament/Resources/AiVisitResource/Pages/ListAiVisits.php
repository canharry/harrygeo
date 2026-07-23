<?php

namespace App\Filament\Resources\AiVisitResource\Pages;

use App\Filament\Resources\AiVisitResource;
use App\Filament\Widgets\AiVisitDailyChart;
use App\Filament\Widgets\AiVisitPlatformChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiVisits extends ListRecords
{
    protected static string $resource = AiVisitResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AiVisitDailyChart::class,
            AiVisitPlatformChart::class,
        ];
    }

    protected function getHeaderWidgetsColumns(): int | string | array
    {
        return 2;
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
