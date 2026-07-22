<?php

namespace App\Filament\Resources\AiVisitResource\Pages;

use App\Filament\Resources\AiVisitResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiVisits extends ListRecords
{
    protected static string $resource = AiVisitResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
