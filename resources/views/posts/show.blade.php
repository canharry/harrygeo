@extends('layouts.app')

@section('title', $post->title . ' - 阳光每一天')

@section('content')
    <!-- 文章详情页 Hero 小横幅 -->
    <section class="page-hero">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="post-breadcrumb">
                <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> 首页</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                <i class="bi bi-chevron-right"></i>
                <span>正文</span>
            </div>
            <h1 class="post-detail-title">{{ $post->title }}</h1>
            <div class="post-detail-meta">
                <span><i class="bi bi-person-circle"></i> {{ $post->user->name ?? '博主' }}</span>
                <span><i class="bi bi-calendar3"></i> {{ $post->published_at->format('Y-m-d') }}</span>
                <span><i class="bi bi-eye"></i> {{ $post->views }} 阅读</span>
                <span><i class="bi bi-heart"></i> {{ $post->likes }} 点赞</span>
            </div>

            @if ($post->aiReferences->count())
                <div class="ai-references">
                    <span class="ai-references-label"><i class="bi bi-robot"></i> AI 收录：</span>
                    @foreach ($post->aiReferences as $ai)
                        <span class="ai-reference-badge" title="{{ $ai->name }} 收录 {{ $ai->count }} 次">
                            {{ $ai->name }} <em>{{ $ai->count }}</em>
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <div class="container main-layout">
        <!-- 左侧边栏：保持与首页一致，提升整体感 -->
        <aside class="sidebar">
            <!-- 博主信息卡片 -->
            <div class="card profile-card">
                <div class="profile-cover"></div>
                <x-image-placeholder :src="$blogger['avatar']" alt="博主头像" type="avatar" class="profile-avatar" />
                <div class="profile-body">
                    <h2 class="profile-name">{{ $blogger['nickname'] }}</h2>
                    <p class="profile-bio">{{ $blogger['signature'] }}</p>
                    <div class="profile-meta">
                        <div><strong>{{ $blogger['articles'] }}</strong><span>文章</span></div>
                        <div><strong>{{ $blogger['categories'] }}</strong><span>分类</span></div>
                        <div><strong>{{ $blogger['tags_count'] }}</strong><span>标签</span></div>
                    </div>
                </div>
            </div>

            <!-- 目录导航（后续可接入文章目录） -->
            <div class="card toc-card">
                <h3 class="card-title"><i class="bi bi-list-ul"></i> 文章目录</h3>
                <p class="toc-placeholder">目录功能将在后续接入 Markdown 解析后自动生成。</p>
            </div>

            <!-- 相关文章 -->
            <div class="card hot-card">
                <h3 class="card-title"><i class="bi bi-link-45deg"></i> 相关文章</h3>
                @if ($relatedPosts->count())
                    <ul class="hot-list">
                        @foreach ($relatedPosts as $related)
                            <li class="hot-item">
                                <x-image-placeholder :src="$related->cover_image" :alt="$related->title" type="thumb" class="hot-thumb" />
                                <div class="hot-info">
                                    <a href="{{ route('posts.show', $related->slug) }}" class="hot-title">{{ Str::limit($related->title, 30) }}</a>
                                    <span class="hot-views"><i class="bi bi-eye"></i> {{ $related->views }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="empty-tip">暂无相关文章</p>
                @endif
            </div>
        </aside>

        <!-- 右侧文章内容 -->
        <article class="content-area">
            <div class="post-detail-card">
                @if ($post->cover_image)
                    <!-- 文章封面 -->
                    <div class="post-detail-cover">
                        <x-image-placeholder :src="$post->cover_image" :alt="$post->title" type="cover" />
                    </div>
                @endif

                <!-- 文章正文 -->
                <div class="post-detail-body">
                    {!! $post->content !!}
                </div>

                <!-- 标签 -->
                <div class="post-detail-tags">
                    <i class="bi bi-tags"></i>
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" class="post-tag" style="--tag-color: {{ $tag->color ?? '#667eea' }}">{{ $tag->name }}</a>
                    @endforeach
                </div>

                <!-- 点赞与分享 -->
                <div class="post-detail-actions">
                    <button class="action-btn like-btn" type="button" data-like-url="{{ route('posts.like', $post->slug) }}">
                        <i class="bi bi-heart"></i> 点赞 <span class="like-count">{{ $post->likes }}</span>
                    </button>
                    <div class="share-btns">
                        <span>分享到：</span>
                        <button class="share-btn" title="复制链接"><i class="bi bi-link-45deg"></i></button>
                    </div>
                </div>
            </div>

            <!-- 上一篇 / 下一篇 -->
            <div class="post-navigation">
                @if ($prevPost)
                    <a href="{{ route('posts.show', $prevPost->slug) }}" class="nav-prev">
                        <span class="nav-label"><i class="bi bi-arrow-left"></i> 上一篇</span>
                        <span class="nav-title">{{ Str::limit($prevPost->title, 40) }}</span>
                    </a>
                @else
                    <div class="nav-prev nav-disabled">
                        <span class="nav-label"><i class="bi bi-arrow-left"></i> 上一篇</span>
                        <span class="nav-title">没有了</span>
                    </div>
                @endif

                @if ($nextPost)
                    <a href="{{ route('posts.show', $nextPost->slug) }}" class="nav-next">
                        <span class="nav-label">下一篇 <i class="bi bi-arrow-right"></i></span>
                        <span class="nav-title">{{ Str::limit($nextPost->title, 40) }}</span>
                    </a>
                @else
                    <div class="nav-next nav-disabled">
                        <span class="nav-label">下一篇 <i class="bi bi-arrow-right"></i></span>
                        <span class="nav-title">没有了</span>
                    </div>
                @endif
            </div>

            <!-- 评论区 -->
            <div class="comment-section">
                <h3 class="section-title"><i class="bi bi-chat-square-text"></i> 评论列表（{{ $post->comments->count() }}）</h3>

                @if ($post->comments->count())
                    <ul class="comment-list">
                        @foreach ($post->comments as $comment)
                            <li class="comment-item">
                                <div class="comment-avatar">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <strong>{{ $comment->user->name ?? '匿名访客' }}</strong>
                                        <span>{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <p>{{ $comment->content }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="empty-tip">暂无评论，快来抢沙发吧~</p>
                @endif

                <!-- 评论表单 -->
                <form action="#" method="post" class="comment-form">
                    @csrf
                    <textarea name="content" rows="4" placeholder="写下你的想法..."></textarea>
                    <button type="submit" class="submit-btn"><i class="bi bi-send"></i> 发表评论</button>
                </form>
            </div>
        </article>
    </div>
@endsection
