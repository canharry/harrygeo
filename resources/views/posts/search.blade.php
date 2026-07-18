@extends('layouts.app')

@section('title', $query ? '"' . $query . '" 的搜索结果 - 阳光每一天' : '搜索文章 - 阳光每一天')

@section('content')
    @php
        /**
         * 生成搜索结果摘要：优先从正文中提取包含关键词的上下文片段
         */
        function searchSnippet($content, $query, $length = 140)
        {
            $text = strip_tags($content);
            if (! $query) {
                return \Illuminate\Support\Str::limit($text, $length);
            }

            $pos = mb_stripos($text, $query);
            if ($pos === false) {
                return \Illuminate\Support\Str::limit($text, $length);
            }

            $start = max(0, $pos - 50);
            $snippet = mb_substr($text, $start, $length);

            return ($start > 0 ? '…' : '') . $snippet . (mb_strlen($text) > $start + $length ? '…' : '');
        }

        /**
         * 对搜索关键词进行高亮，输出安全 HTML
         */
        function searchHighlight($text, $query)
        {
            $escaped = e($text);
            if (! $query) {
                return $escaped;
            }

            $pattern = '/' . preg_quote(e($query), '/') . '/iu';
            return preg_replace($pattern, '<mark class="search-mark">$0</mark>', $escaped);
        }
    @endphp

    <!-- 搜索页 Hero 小横幅 -->
    <section class="page-hero">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="post-breadcrumb">
                <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> 首页</a>
                <i class="bi bi-chevron-right"></i>
                <span>搜索</span>
            </div>
            <h1 class="archive-title"><i class="bi bi-search"></i> 搜索结果</h1>
            @if ($query)
                <p class="archive-desc">关键词 “<strong>{{ $query }}</strong>” 共找到 {{ $posts->total() }} 篇文章</p>
            @else
                <p class="archive-desc">请输入关键词搜索全站文章</p>
            @endif
        </div>
    </section>

    <div class="container main-layout">
        <!-- 左侧边栏 -->
        <aside class="sidebar">
            <div class="card search-card">
                <h3 class="card-title"><i class="bi bi-search"></i> 搜索文章</h3>
                <form action="{{ route('posts.search') }}" method="get" class="search-form">
                    <input type="text" name="q" value="{{ $query }}" placeholder="输入关键词..." class="search-input">
                    <button type="submit" class="search-btn"><i class="bi bi-arrow-right-short"></i></button>
                </form>
            </div>
        </aside>

        <!-- 右侧文章列表 -->
        <section class="content-area">
            <div class="section-header">
                <h2><i class="bi bi-stars"></i> 搜索结果</h2>
                @if ($query)
                    <span class="result-count">共 {{ $posts->total() }} 篇</span>
                @endif
            </div>

            @if (! $query)
                <div class="empty-state">
                    <i class="bi bi-search"></i>
                    <p>请输入关键词开始搜索</p>
                    <a href="{{ route('posts.index') }}" class="btn btn-primary">查看全部文章</a>
                </div>
            @elseif ($posts->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>未找到与 “{{ $query }}” 相关的文章</p>
                    <a href="{{ route('posts.index') }}" class="btn btn-primary">查看全部文章</a>
                </div>
            @else
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
                                <h3 class="post-title">
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        {!! searchHighlight($post->title, $query) !!}
                                    </a>
                                </h3>
                                <p class="post-summary search-snippet">
                                    {!! searchHighlight(searchSnippet($post->content, $query), $query) !!}
                                </p>
                                <div class="post-tags">
                                    @foreach ($post->tags->take(3) as $tag)
                                        <a href="{{ route('tags.show', $tag->slug) }}" class="post-tag" style="--tag-color: {{ $tag->color ?? '#667eea' }}">
                                            {!! searchHighlight($tag->name, $query) !!}
                                        </a>
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

                <div class="pagination-wrapper">
                    {{ $posts->links('pagination.custom') }}
                </div>
            @endif
        </section>
    </div>
@endsection
