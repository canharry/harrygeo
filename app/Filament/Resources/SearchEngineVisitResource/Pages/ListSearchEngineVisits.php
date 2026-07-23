<?php

namespace App\Filament\Resources\SearchEngineVisitResource\Pages;

use App\Filament\Resources\SearchEngineVisitResource;
use App\Filament\Widgets\SearchEngineVisitDailyChart;
use App\Filament\Widgets\SearchEngineVisitPlatformChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSearchEngineVisits extends ListRecords
{
    protected static string $resource = SearchEngineVisitResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            SearchEngineVisitDailyChart::class,
            SearchEngineVisitPlatformChart::class,
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
