@extends('layouts.app')

@section('title', '阳光每一天 - 首页')

@section('content')
    <!-- 顶部 Hero 横幅：使用 CSS 渐变 + JS 飘落花瓣实现动态背景 -->
    <section class="hero" id="heroBanner">
        <canvas class="hero-canvas" id="heroCanvas"></canvas>
        <div class="hero-blob blob-1"></div>
        <div class="hero-blob blob-2"></div>
        <div class="hero-blob blob-3"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title">阳光每一天</h1>
            <p class="hero-subtitle">意外和明天谁先到来？我们不知道，我们要阳光每一天。</p>
            <div class="hero-stats">
                <a href="{{ route('posts.index') }}"><i class="bi bi-file-earmark-text"></i> {{ $blogger['articles'] }} 篇文章</a>
                <a href="{{ $blogger['user_id'] ? route('users.categories', $blogger['user_id']) : route('categories.index') }}"><i class="bi bi-folder"></i> {{ $blogger['categories'] }} 个分类</a>
                <a href="{{ $blogger['user_id'] ? route('users.tags', $blogger['user_id']) : route('tags.index') }}"><i class="bi bi-tags"></i> {{ $blogger['tags_count'] }} 个标签</a>
            </div>
        </div>
    </section>

    <!-- 主体内容区：左侧边栏 + 右侧文章网格 -->
    <div class="container main-layout">
        <!-- 左侧边栏 -->
        <aside class="sidebar">
            <!-- 博主信息卡片 -->
            <div class="card profile-card">
                <div class="profile-cover"></div>
                <x-image-placeholder :src="$blogger['avatar']" alt="博主头像" type="avatar" class="profile-avatar" />
                <div class="profile-body">
                    <h2 class="profile-name">{{ $blogger['nickname'] }}</h2>
                    <p class="profile-bio">{{ $blogger['signature'] }}</p>
                    <div class="profile-meta">
                        <a href="{{ route('posts.index') }}"><strong>{{ $blogger['articles'] }}</strong><span>文章</span></a>
                        <a href="{{ $blogger['user_id'] ? route('users.categories', $blogger['user_id']) : route('categories.index') }}"><strong>{{ $blogger['categories'] }}</strong><span>分类</span></a>
                        <a href="{{ $blogger['user_id'] ? route('users.tags', $blogger['user_id']) : route('tags.index') }}"><strong>{{ $blogger['tags_count'] }}</strong><span>标签</span></a>
                    </div>
                </div>
            </div>

            <!-- 搜索框 -->
            <div class="card search-card">
                <h3 class="card-title"><i class="bi bi-search"></i> 搜索文章</h3>
                <form action="{{ route('posts.search') }}" method="get" class="search-form">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="输入关键词..." class="search-input">
                    <button type="submit" class="search-btn"><i class="bi bi-arrow-right-short"></i></button>
                </form>
            </div>

            <!-- 热门推荐 -->
            <div class="card hot-card">
                <h3 class="card-title"><i class="bi bi-fire"></i> 热门推荐</h3>
                <ul class="hot-list">
                    @foreach ($hotPosts as $post)
                        <li class="hot-item">
                            <x-image-placeholder :src="$post->cover_image" :alt="$post->title" type="thumb" class="hot-thumb" />
                            <div class="hot-info">
                                <a href="{{ route('posts.show', $post->slug) }}" class="hot-title">{{ Str::limit($post->title, 30) }}</a>
                                <span class="hot-views"><i class="bi bi-eye"></i> {{ $post->views }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- 个人分类 -->
            <div class="card category-card">
                <h3 class="card-title"><i class="bi bi-grid-3x3-gap"></i> 个人分类</h3>
                <ul class="category-list">
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('categories.show', $category->slug) }}" style="--cat-color: {{ $category->color ?? '#ff7eb3' }}">
                                <i class="bi {{ $category->icon ?? 'bi-folder' }}"></i>
                                <span>{{ $category->name }}</span>
                                <span class="category-count">{{ $category->posts_count ?? $category->posts->count() }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- 标签云 -->
            <div class="card tag-card">
                <h3 class="card-title"><i class="bi bi-cloud"></i> 标签云</h3>
                <div class="tag-cloud">
                    @foreach ($tags as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" class="tag-item" style="--tag-color: {{ $tag->color ?? '#667eea' }}">{{ $tag->name }}</a>
                    @endforeach
                </div>
            </div>

            <!-- 友情链接 -->
            <div class="card friend-card">
                <h3 class="card-title"><i class="bi bi-link-45deg"></i> 友情链接</h3>
                <ul class="friend-list">
                    @foreach ($friends as $friend)
                        <li><a href="{{ $friend['url'] }}" target="_blank" rel="noopener"><i class="bi bi-arrow-up-right-circle"></i> {{ $friend['name'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- 右侧文章列表 -->
        <section class="content-area">
            <!-- 文章分类标题栏 -->
            <div class="section-header">
                <h2><i class="bi bi-stars"></i> 最新文章</h2>
                <span class="more-link">最新文章</span>
            </div>

            <!-- 文章卡片网格 -->
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
                                <span><i class="bi bi-chat-dots"></i> {{ $post->comments_count ?? $post->comments->count() }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- 分页 -->
            <div class="pagination-wrapper">
                {{ $posts->links('pagination.custom') }}
            </div>
        </section>
    </div>
@endsection
