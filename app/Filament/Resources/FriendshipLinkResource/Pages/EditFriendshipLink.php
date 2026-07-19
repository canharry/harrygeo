<?php

namespace App\Filament\Resources\FriendshipLinkResource\Pages;

use App\Filament\Resources\FriendshipLinkResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFriendshipLink extends EditRecord
{
    protected static string $resource = FriendshipLinkResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
