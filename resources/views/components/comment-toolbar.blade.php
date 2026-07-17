{{--
    评论工具栏组件
    提供表情与图片上传按钮，作用于指定的 textarea
    参数：
    - textareaId 目标 textarea 的 id（必填）
    - uploadUrl  图片上传接口地址（默认 comments.upload-image 路由）
--}}
@props(['textareaId', 'uploadUrl' => route('comments.upload-image')])

<div class="comment-toolbar" data-target="{{ $textareaId }}" data-upload-url="{{ $uploadUrl }}">
    <button type="button" class="comment-toolbar-btn comment-toolbar-btn--emoji" title="插入表情">
        <i class="bi bi-emoji-smile"></i>
    </button>
    <button type="button" class="comment-toolbar-btn comment-toolbar-btn--image" title="插入图片">
        <i class="bi bi-image"></i>
    </button>
    <input type="file" class="comment-image-input" accept="image/png,image/jpeg,image/gif,image/webp">

    <div class="emoji-popup">
        <div class="emoji-grid">
            @foreach ([
                '😀', '😂', '🤣', '🥰', '😍', '🤔',
                '😎', '😭', '😡', '😅', '😉', '😊',
                '🥳', '🤩', '🙄', '😴', '🤯', '🎉',
                '🔥', '❤️', '💔', '🌹', '🍺', '🎂',
                '🎁', '🌟', '🌈', '👍', '👎', '🙏',
                '👏',
            ] as $emoji)
                <button type="button" class="emoji-item">{{ $emoji }}</button>
            @endforeach
        </div>
    </div>
</div>
