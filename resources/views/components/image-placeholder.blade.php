{{--
    图片占位组件
    有真实图片地址时显示 img，否则显示 CSS 渐变占位图
    type 可选：card（文章卡片封面）、thumb（侧边栏缩略图）、cover（详情页封面）、avatar（头像）
    class 可传入调用处原有的样式类名
--}}
@props(['src' => null, 'alt' => '', 'type' => 'card', 'class' => ''])

@php
    $imageUrl = $src;
    if ($src && ! filter_var($src, FILTER_VALIDATE_URL) && ! str_starts_with($src, '/')) {
        $imageUrl = asset('storage/' . $src);
    }
@endphp

@if ($src)
    <img src="{{ $imageUrl }}" alt="{{ $alt }}" class="placeholder-img placeholder-{{ $type }} {{ $class }}">
@else
    <div class="placeholder-block placeholder-{{ $type }} {{ $class }}" aria-label="{{ $alt ?: '暂无图片' }}">
        <i class="bi bi-image"></i>
    </div>
@endif
