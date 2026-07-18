@extends('layouts.app')

@section('title', $user->name . ' 的标签 - 阳光每一天')

@section('content')
    <!-- 用户标签列表页 Hero 小横幅 -->
    <section class="page-hero">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="post-breadcrumb">
                <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> 首页</a>
                <i class="bi bi-chevron-right"></i>
                <span>{{ $user->name }} 的标签</span>
            </div>
            <h1 class="archive-title"><i class="bi bi-cloud"></i> {{ $user->name }} 的标签</h1>
            <p class="archive-desc">该博主共使用过 {{ $tags->count() }} 个标签</p>
        </div>
    </section>

    <div class="container main-layout">
        <aside class="sidebar">
            <div class="card profile-card-mini">
                <x-image-placeholder :src="$user->avatar" alt="{{ $user->name }}" type="avatar" class="profile-avatar-mini" />
                <h3 class="profile-name">{{ $user->name }}</h3>
                <p class="profile-bio">{{ $user->signature ?? '暂无签名' }}</p>
                <a href="{{ route('users.categories', $user) }}" class="btn-outline"><i class="bi bi-grid-3x3-gap"></i> 查看分类</a>
            </div>
        </aside>

        <section class="content-area">
            @if ($tags->count())
                <div class="tag-cloud tag-cloud-page">
                    @foreach ($tags as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" class="tag-item" style="--tag-color: {{ $tag->color ?? '#667eea' }}">{{ $tag->name }}</a>
                    @endforeach
                </div>

                <div class="section-header" style="margin-top: 24px;">
                    <h2><i class="bi bi-journal-text"></i> 标签下的文章</h2>
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
                        <p>暂无文章</p>
                    </div>
                @endif
            @else
                <div class="empty-card">
                    <i class="bi bi-inbox"></i>
                    <p>该博主暂无标签</p>
                </div>
            @endif
        </section>
    </div>
@endsection
