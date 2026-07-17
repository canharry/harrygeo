<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldAvatar = $this->record->avatar;
        $avatarState = $data['avatar'] ?? [];
        $newAvatar = is_array($avatarState) ? reset($avatarState) : $avatarState;
        $newAvatar = $newAvatar ?: null;
        $data['avatar'] = $newAvatar;

        // 头像变更或清空时删除旧头像
        if ($oldAvatar && $oldAvatar !== $newAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return $data;
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
