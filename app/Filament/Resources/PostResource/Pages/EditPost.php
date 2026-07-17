<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    /**
     * 缩短顶部面包屑中的文章标题，避免长标题撑高头部区域
     */
    protected function getBreadcrumbs(): array
    {
        return collect(parent::getBreadcrumbs())
            ->map(function ($label) {
                return is_string($label) ? Str::limit($label, 28) : $label;
            })
            ->all();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = \App\Models\Post::generateUniqueSlug($data['title'], $this->record->id);
        }

        if (! empty($data['cover_image_file'])) {
            $data['cover_image'] = $data['cover_image_file'];
        }
        unset($data['cover_image_file']);

        return $data;
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
