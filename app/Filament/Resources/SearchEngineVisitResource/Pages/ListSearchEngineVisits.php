<?php

namespace App\Filament\Resources\SearchEngineVisitResource\Pages;

use App\Filament\Resources\SearchEngineVisitResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSearchEngineVisits extends ListRecords
{
    protected static string $resource = SearchEngineVisitResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
