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

        // 视频：本地上传优先，其次外部链接
        if (! empty($data['video_file'])) {
            $data['video'] = $data['video_file'];
        } elseif (! empty($data['video_url'])) {
            $data['video'] = $data['video_url'];
        } else {
            $data['video'] = null;
        }
        unset($data['video_file'], $data['video_url']);

        return $data;
    }
}
