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
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'table',
        'underline',
        'undo',
        'videoUpload',
        'videoLink',
    ];
}
