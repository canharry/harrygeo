@extends('layouts.app')

@section('title', '消息中心 - 阳光每一天')

@section('content')
    <!-- 消息中心 Hero 小横幅 -->
    <section class="page-hero">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <h1 class="post-detail-title"><i class="bi bi-bell"></i> 消息中心</h1>
        </div>
    </section>

    <div class="container main-layout">
        <!-- 左侧边栏 -->
        <aside class="sidebar">
            <div class="card">
                <h3 class="card-title"><i class="bi bi-info-circle"></i> 提示</h3>
                <p class="empty-tip">这里只展示与您相关的评论与回复。</p>
            </div>
        </aside>

        <!-- 右侧消息列表 -->
        <article class="content-area">
            <div class="comment-section">
                <h3 class="section-title"><i class="bi bi-chat-square-text"></i> 相关消息</h3>

                @if ($messages->count())
                    <ul class="comment-list">
                        @foreach ($messages as $message)
                            <li class="comment-item" id="message-{{ $message->id }}">
                                <div class="comment-avatar">
                                    <x-image-placeholder :src="$message->user->avatar" alt="{{ $message->user->name }}" type="avatar" class="comment-avatar-img" />
                                </div>
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <strong>{{ $message->user->name }}</strong>
                                        <span>{{ $message->created_at->format('Y-m-d H:i') }}</span>
                                    </div>

                                    <div class="comment-text">
                                        @if ($message->parent)
                                            <span class="reply-to">{{ $message->parent->user->name ?? '匿名访客' }}</span>
                                        @endif
                                        {!! $message->parseContent() !!}
                                    </div>

                                    <div class="comment-actions">
                                        <a href="{{ route('posts.show', $message->post->slug) }}#comment-{{ $message->id }}" class="reply-toggle">
                                            <i class="bi bi-box-arrow-in-right"></i> 查看原文
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="pagination-wrapper">
                        {{ $messages->links('pagination.custom') }}
                    </div>
                @else
                    <div class="empty-card">
                        <i class="bi bi-inbox"></i>
                        <p>暂无相关消息</p>
                    </div>
                @endif
            </div>
        </article>
    </div>
@endsection
