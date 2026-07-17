<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (empty($data['slug'])) {
            $data['slug'] = \App\Models\Post::generateUniqueSlug($data['title']);
        }

        if (! empty($data['cover_image_file'])) {
            $data['cover_image'] = $data['cover_image_file'];
        }
        unset($data['cover_image_file']);

        return $data;
    }
}
