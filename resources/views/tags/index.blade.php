@extends('layouts.app')

@section('title', '全部标签 - 阳光每一天')

@section('content')
    <!-- 标签列表页 Hero 小横幅 -->
    <section class="page-hero">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="post-breadcrumb">
                <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> 首页</a>
                <i class="bi bi-chevron-right"></i>
                <span>全部标签</span>
            </div>
            <h1 class="archive-title"><i class="bi bi-cloud"></i> 全部标签</h1>
            <p class="archive-desc">共 {{ $tags->count() }} 个标签</p>
        </div>
    </section>

    <div class="container main-layout">
        <aside class="sidebar">
            <div class="card">
                <h3 class="card-title"><i class="bi bi-info-circle"></i> 提示</h3>
                <p class="empty-tip">点击标签可查看该标签下的所有文章。</p>
            </div>
        </aside>

        <section class="content-area">
            @if ($tags->count())
                <div class="tag-cloud tag-cloud-page">
                    @foreach ($tags as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" class="tag-item" style="--tag-color: {{ $tag->color ?? '#667eea' }}">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @else
                <div class="empty-card">
                    <i class="bi bi-inbox"></i>
                    <p>暂无标签</p>
                </div>
            @endif
        </section>
    </div>
@endsection
