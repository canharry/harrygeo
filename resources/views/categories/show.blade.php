@extends('layouts.app')

@section('title', $category->name . ' - 分类归档 - 阳光每一天')

@section('content')
    <!-- 分类归档页 Hero 小横幅 -->
    <section class="page-hero" style="--page-hero-color: {{ $category->color ?? '#667eea' }}">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="post-breadcrumb">
                <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> 首页</a>
                <i class="bi bi-chevron-right"></i>
                <span>分类</span>
            </div>
            <h1 class="archive-title">
                <i class="bi {{ $category->icon ?? 'bi-folder' }}"></i>
                {{ $category->name }}
            </h1>
            <p class="archive-desc">
                {{ $category->description ?? '该分类下共有 ' . $posts->total() . ' 篇文章' }}
            </p>
        </div>
    </section>

    <div class="container main-layout">
        <!-- 左侧边栏 -->
        <aside class="sidebar">
            <div class="card category-card">
                <h3 class="card-title"><i class="bi bi-grid-3x3-gap"></i> 全部分类</h3>
                <ul class="category-list">
                    @foreach ($categories as $item)
                        <li>
                            <a href="{{ route('categories.show', $item->slug) }}" class="{{ $item->id === $category->id ? 'active' : '' }}" style="--cat-color: {{ $item->color ?? '#ff7eb3' }}">
                                <i class="bi {{ $item->icon ?? 'bi-folder' }}"></i>
                                <span>{{ $item->name }}</span>
                                <span class="category-count">{{ $item->posts_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- 右侧文章列表 -->
        <section class="content-area">
            <div class="section-header">
                <h2><i class="bi bi-journal-text"></i> 文章列表</h2>
                <span class="result-count">共 {{ $posts->total() }} 篇</span>
            </div>

            @if ($posts->count())
                <div class="post-grid">
                    @foreach ($posts as $post)
                        <article class="post-card">
                            <a href="{{ route('posts.show', $post->slug) }}" class="post-image-wrapper">
                                <x-image-placeholder :src="$post->cover_image" :alt="$post->title" type="card" class="post-image" />
                                <span class="post-category" style="--cat-color: {{ $post->category->color ?? '#ff7eb3' }}">
                                    {{ $post->category->name }}
                                </span>
                            </a>
                            <div class="post-body">
                                <h3 class="post-title"><a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a></h3>
                                <p class="post-summary">{{ Str::limit($post->summary, 70) }}</p>
                                <div class="post-tags">
                                    @foreach ($post->tags->take(3) as $tag)
                                        <a href="{{ route('tags.show', $tag->slug) }}" class="post-tag" style="--tag-color: {{ $tag->color ?? '#667eea' }}">{{ $tag->name }}</a>
                                    @endforeach
                                </div>
                                <div class="post-meta">
                                    <span><i class="bi bi-calendar3"></i> {{ $post->published_at->format('Y-m-d') }}</span>
                                    <span><i class="bi bi-eye"></i> {{ $post->views }}</span>
                                    <span><i class="bi bi-heart"></i> {{ $post->likes }}</span>
                                    <span><i class="bi bi-chat-dots"></i> {{ $post->comments_count }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrapper">
                    {{ $posts->links('pagination.custom') }}
                </div>
            @else
                <div class="empty-card">
                    <i class="bi bi-inbox"></i>
                    <p>该分类下暂无文章</p>
                </div>
            @endif
        </section>
    </div>
@endsection
