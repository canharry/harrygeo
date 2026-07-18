<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\RichEditor as BaseRichEditor;

class RichEditor extends BaseRichEditor
{
    protected array | \Closure $toolbarButtons = [
        'attachFiles',
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'h2',
        'h3',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'underline',
        'undo',
        'videoUpload',
        'videoLink',
    ];
}
