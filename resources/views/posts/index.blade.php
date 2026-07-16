@extends('layouts.app')

@section('title', '全部文章 - 阳光每一天')

@section('content')
    <!-- 文章列表页 Hero 小横幅 -->
    <section class="page-hero">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="post-breadcrumb">
                <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> 首页</a>
                <i class="bi bi-chevron-right"></i>
                <span>文章</span>
            </div>
            <h1 class="archive-title"><i class="bi bi-journal-text"></i> 全部文章</h1>
            <p class="archive-desc">记录技术成长与生活点滴</p>
        </div>
    </section>

    <div class="container main-layout">
        <!-- 左侧边栏占位：保持与首页布局一致 -->
        <aside class="sidebar">
            <div class="card">
                <h3 class="card-title"><i class="bi bi-info-circle"></i> 提示</h3>
                <p class="toc-placeholder">这里是全部文章列表，点击文章卡片可查看详情。</p>
            </div>
        </aside>

        <!-- 右侧文章列表 -->
        <section class="content-area">
            <div class="section-header">
                <h2><i class="bi bi-stars"></i> 文章列表</h2>
                <span class="result-count">共 {{ $posts->total() }} 篇</span>
            </div>

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
        </section>
    </div>
@endsection
