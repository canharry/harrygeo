<?php

namespace App\Filament\Resources\FriendshipLinkResource\Pages;

use App\Filament\Resources\FriendshipLinkResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFriendshipLinks extends ListRecords
{
    protected static string $resource = FriendshipLinkResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
