@extends('layouts.app')

@section('title', $post->title . ' - 阳光每一天')

@php
    $canonicalUrl = route('posts.show', $post->slug);
    $seoDescription = $post->summary ?: Str::limit(strip_tags($post->content), 150);
    $seoKeywords = $post->tags->pluck('name')->implode(',');
    $seoImage = $post->cover_image;
    if ($seoImage && ! filter_var($seoImage, FILTER_VALIDATE_URL) && ! str_starts_with($seoImage, '/')) {
        $seoImage = asset('storage/' . $seoImage);
    }
    $siteName = config('app.name', '阳光每一天');
@endphp

@section('meta_description', $seoDescription)
@section('meta_keywords', $seoKeywords)
@section('canonical', $canonicalUrl)

@push('meta')
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="zh_CN">
    @if ($seoImage)
        <meta property="og:image" content="{{ $seoImage }}">
    @endif
    <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    <meta property="article:section" content="{{ $post->category->name }}">
    @foreach ($post->tags as $tag)
        <meta property="article:tag" content="{{ $tag->name }}">
    @endforeach

    <!-- Twitter Card -->
    <meta name="twitter:card" content="{{ $seoImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $post->title }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if ($seoImage)
        <meta name="twitter:image" content="{{ $seoImage }}">
    @endif
@endpush

@push('structured_data')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "{{ e($post->title) }}",
        "description": "{{ e($seoDescription) }}",
        @if ($seoImage)
        "image": ["{{ e($seoImage) }}"],
        @endif
        "author": {
            "@type": "Person",
            "name": "{{ e($post->user->name ?? '博主') }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ e($siteName) }}"
        },
        "datePublished": "{{ $post->published_at->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ e($canonicalUrl) }}"
        }
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "首页",
                "item": "{{ e(route('home')) }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "{{ e($post->category->name) }}",
                "item": "{{ e(route('categories.show', $post->category->slug)) }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ e($post->title) }}",
                "item": "{{ e($canonicalUrl) }}"
            }
        ]
    }
    </script>
@endpush

@section('content')
    <!-- 文章详情页 Hero 小横幅 -->
    <section class="page-hero">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <nav class="post-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> 首页</a>
                <i class="bi bi-chevron-right"></i>
                <a href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                <i class="bi bi-chevron-right"></i>
                <span aria-current="page">正文</span>
            </nav>
            <div class="post-detail-title-wrap">
                <h1 class="post-detail-title">{{ $post->title }}</h1>

                <!-- 原创 / 转载徽章（Hero 区域） -->
                <div class="post-detail-copyright hero-copyright-badge">
                    @if ($post->is_original)
                        <span class="copyright-badge original" title="原创文章">
                            <i class="bi bi-pencil-square"></i> 原创
                        </span>
                    @else
                        <span class="copyright-badge reprint" title="转载文章">
                            <i class="bi bi-share"></i> 转载
                        </span>
                        @if ($post->original_url)
                            <a href="{{ $post->original_url }}" target="_blank" rel="noopener noreferrer" class="original-link" title="查看原文">
                                <i class="bi bi-box-arrow-up-right"></i> 原文
                            </a>
                        @endif
                    @endif
                </div>
            </div>
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

            <!-- 目录导航 -->
            <div class="card toc-card" id="toc-card">
                <h3 class="card-title"><i class="bi bi-list-ul"></i> 文章目录</h3>
                <nav class="toc-nav" id="toc-nav">
                    <p class="toc-placeholder">本文暂无目录</p>
                </nav>
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

                @if ($post->video && $post->videoType() !== 'none')
                    <!-- 文章视频 -->
                    <div class="post-detail-video">
                        @if ($post->videoType() === 'youtube')
                            <div class="video-embed">
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $post->youtubeVideoId() }}"
                                    title="YouTube video player"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            </div>
                        @elseif ($post->videoType() === 'bilibili')
                            <div class="video-embed">
                                <iframe
                                    src="https://player.bilibili.com/player.html?bvid={{ $post->bilibiliVideoId() }}&page=1&high_quality=1"
                                    scrolling="no"
                                    border="0"
                                    frameborder="no"
                                    framespacing="0"
                                    allowfullscreen="true"
                                ></iframe>
                            </div>
                        @else
                            <div class="video-player">
                                <video controls preload="metadata" poster="{{ $post->cover_image ?? '' }}">
                                    <source src="{{ $post->videoUrl() }}" type="video/mp4">
                                    您的浏览器不支持 HTML5 视频播放，请<a href="{{ $post->videoUrl() }}" target="_blank">点击下载</a>观看。
                                </video>
                            </div>
                        @endif
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

                <!-- 侵权举报文案 -->
                @if ($infringementNotice)
                    <div class="post-detail-notice">
                        <i class="bi bi-shield-exclamation"></i>
                        <span>{!! $infringementNotice !!}</span>
                    </div>
                @endif

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
                <h3 class="section-title"><i class="bi bi-chat-square-text"></i> 评论列表（{{ $post->comments_count }}）</h3>

                @if (session('success'))
                    <div class="alert-success" style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: #d1fae5; color: #065f46; border-radius: 0.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                @php
                    $topLevelComments = $post->comments->whereNull('parent_id');
                    $totalComments = $post->comments_count;
                @endphp

                @if ($totalComments)
                    <ul class="comment-list">
                        @foreach ($topLevelComments as $comment)
                            @include('posts._comment', [
                                'comment' => $comment,
                                'post' => $post,
                                'depth' => 0,
                                'replyTo' => null,
                            ])
                        @endforeach
                    </ul>
                @else
                    <p class="empty-tip">暂无评论，快来抢沙发吧~</p>
                @endif

                <!-- 主评论表单 -->
                @auth
                    <form action="{{ route('posts.comments.store', $post->slug) }}" method="post" class="comment-form main-comment-form">
                        @csrf
                        <x-comment-toolbar textarea-id="main-comment-content" />
                        <textarea id="main-comment-content" name="content" rows="4" placeholder="写下你的想法..." required maxlength="1000">{{ old('parent_id') ? '' : old('content') }}</textarea>
                        @error('content')
                            @if (! old('parent_id'))
                                <p class="error-text">{{ $message }}</p>
                            @endif
                        @enderror
                        <button type="submit" class="submit-btn"><i class="bi bi-send"></i> 发表评论</button>
                    </form>
                @else
                    <p class="empty-tip">请 <a href="{{ route('login') }}">登录</a> 后发表评论~</p>
                @endauth
            </div>
        </article>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function showActions(actionsId) {
                if (actionsId) {
                    var actions = document.getElementById(actionsId);
                    if (actions) {
                        actions.style.display = 'flex';
                    }
                }
            }

            function hideActions(actionsId) {
                if (actionsId) {
                    var actions = document.getElementById(actionsId);
                    if (actions) {
                        actions.style.display = 'none';
                    }
                }
            }

            // 展开回复表单
            document.querySelectorAll('.reply-toggle').forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetId = this.getAttribute('data-target');
                    var actionsId = this.getAttribute('data-actions');
                    var form = document.getElementById(targetId);
                    if (form) {
                        form.style.display = 'block';
                        hideActions(actionsId);
                        form.querySelector('textarea').focus();
                    }
                });
            });

            // 取消回复
            document.querySelectorAll('.cancel-reply').forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetId = this.getAttribute('data-target');
                    var actionsId = this.getAttribute('data-actions');
                    var form = document.getElementById(targetId);
                    if (form) {
                        form.style.display = 'none';
                        form.querySelector('textarea').value = '';
                        showActions(actionsId);
                    }
                });
            });

            // 展开编辑表单
            document.querySelectorAll('.edit-toggle').forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetId = this.getAttribute('data-target');
                    var actionsId = this.getAttribute('data-actions');
                    var form = document.getElementById(targetId);
                    if (form) {
                        form.style.display = 'block';
                        hideActions(actionsId);
                        form.querySelector('textarea').focus();
                    }
                });
            });

            // 取消编辑
            document.querySelectorAll('.cancel-edit').forEach(function (button) {
                button.addEventListener('click', function () {
                    var targetId = this.getAttribute('data-target');
                    var actionsId = this.getAttribute('data-actions');
                    var form = document.getElementById(targetId);
                    if (form) {
                        form.style.display = 'none';
                        showActions(actionsId);
                    }
                });
            });

            /**
             * 在 textarea 光标处插入文本
             */
            function insertTextAtCursor(textarea, text) {
                textarea.focus();
                var start = textarea.selectionStart;
                var end = textarea.selectionEnd;
                var before = textarea.value.substring(0, start);
                var after = textarea.value.substring(end);
                textarea.value = before + text + after;
                var pos = start + text.length;
                textarea.setSelectionRange(pos, pos);
            }

            // 展开/收起表情弹窗
            document.querySelectorAll('.comment-toolbar-btn--emoji').forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var toolbar = this.closest('.comment-toolbar');
                    var popup = toolbar.querySelector('.emoji-popup');
                    document.querySelectorAll('.emoji-popup.is-open').forEach(function (p) {
                        if (p !== popup) {
                            p.classList.remove('is-open');
                        }
                    });
                    popup.classList.toggle('is-open');
                });
            });

            // 选择表情插入 textarea
            document.querySelectorAll('.emoji-item').forEach(function (item) {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    var toolbar = this.closest('.comment-toolbar');
                    var textareaId = toolbar.getAttribute('data-target');
                    var textarea = document.getElementById(textareaId);
                    if (textarea) {
                        insertTextAtCursor(textarea, this.textContent);
                    }
                    toolbar.querySelector('.emoji-popup').classList.remove('is-open');
                });
            });

            // 触发图片上传选择
            document.querySelectorAll('.comment-toolbar-btn--image').forEach(function (button) {
                button.addEventListener('click', function () {
                    var toolbar = this.closest('.comment-toolbar');
                    toolbar.querySelector('.comment-image-input').click();
                });
            });

            // 图片选择后 AJAX 上传并插入占位符
            document.querySelectorAll('.comment-image-input').forEach(function (input) {
                input.addEventListener('change', function () {
                    var file = this.files[0];
                    if (! file) {
                        return;
                    }

                    var toolbar = this.closest('.comment-toolbar');
                    var textareaId = toolbar.getAttribute('data-target');
                    var textarea = document.getElementById(textareaId);
                    var uploadUrl = toolbar.getAttribute('data-upload-url');
                    var imageBtn = toolbar.querySelector('.comment-toolbar-btn--image');

                    imageBtn.classList.add('is-loading');
                    imageBtn.disabled = true;

                    var formData = new FormData();
                    formData.append('image', file);

                    fetch(uploadUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    })
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            if (data.path && textarea) {
                                insertTextAtCursor(textarea, '[img:' + data.path + ']');
                            } else {
                                alert(data.message || '图片上传失败');
                            }
                        })
                        .catch(function () {
                            alert('图片上传失败，请稍后重试');
                        })
                        .finally(function () {
                            imageBtn.classList.remove('is-loading');
                            imageBtn.disabled = false;
                            input.value = '';
                        });
                });
            });

            // 点击页面其他区域关闭表情弹窗
            document.addEventListener('click', function () {
                document.querySelectorAll('.emoji-popup.is-open').forEach(function (popup) {
                    popup.classList.remove('is-open');
                });
            });

            // 自动生成文章目录
            (function () {
                var body = document.querySelector('.post-detail-body');
                var tocNav = document.getElementById('toc-nav');
                if (!body || !tocNav) {
                    return;
                }

                var headings = Array.prototype.slice.call(body.querySelectorAll('h1, h2, h3, h4, h5, h6'));
                if (headings.length === 0) {
                    return;
                }

                // 为没有 id 的标题生成唯一锚点
                var usedIds = {};
                headings.forEach(function (heading, index) {
                    if (!heading.id) {
                        var base = 'heading-' + heading.tagName.toLowerCase() + '-' + index;
                        var id = base;
                        var counter = 1;
                        while (usedIds[id]) {
                            id = base + '-' + counter++;
                        }
                        usedIds[id] = true;
                        heading.id = id;
                    } else {
                        usedIds[heading.id] = true;
                    }
                });

                // 构建目录列表
                var ul = document.createElement('ul');
                ul.className = 'toc-list';
                headings.forEach(function (heading) {
                    var li = document.createElement('li');
                    li.className = 'toc-item toc-level-' + heading.tagName.toLowerCase();
                    var a = document.createElement('a');
                    a.href = '#' + heading.id;
                    a.textContent = heading.textContent.trim();
                    a.className = 'toc-link';
                    a.addEventListener('click', function (e) {
                        e.preventDefault();
                        var target = document.getElementById(heading.id);
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            history.replaceState(null, null, '#' + heading.id);
                        }
                    });
                    li.appendChild(a);
                    ul.appendChild(li);
                });

                tocNav.innerHTML = '';
                tocNav.appendChild(ul);

                // 滚动时高亮当前目录项
                function highlightActiveToc() {
                    var offset = 100;
                    var activeHeading = null;
                    for (var i = headings.length - 1; i >= 0; i--) {
                        var rect = headings[i].getBoundingClientRect();
                        if (rect.top <= offset) {
                            activeHeading = headings[i];
                            break;
                        }
                    }

                    tocNav.querySelectorAll('.toc-link').forEach(function (link) {
                        link.classList.remove('is-active');
                    });

                    if (activeHeading) {
                        var activeLink = tocNav.querySelector('a[href="#' + activeHeading.id + '"]');
                        if (activeLink) {
                            activeLink.classList.add('is-active');
                        }
                    }
                }

                window.addEventListener('scroll', highlightActiveToc, { passive: true });
                highlightActiveToc();
            })();

            // 将正文中的视频占位符转换为播放器
            (function () {
                var body = document.querySelector('.post-detail-body');
                if (!body) {
                    return;
                }

                function extractYoutubeId(url) {
                    var match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/);
                    return match ? match[1] : null;
                }

                function extractBilibiliId(url) {
                    var match = url.match(/(?:bilibili\.com\/video\/|b23\.tv\/)(BV[a-zA-Z0-9]+)/);
                    return match ? match[1] : null;
                }

                function detectVideoType(url) {
                    if (/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)/.test(url)) {
                        return 'youtube';
                    }
                    if (/(?:bilibili\.com\/video\/|b23\.tv\/)/.test(url)) {
                        return 'bilibili';
                    }
                    return 'html5';
                }

                function createYoutubeEmbed(url) {
                    var id = extractYoutubeId(url);
                    if (!id) {
                        return null;
                    }
                    return '<div class="video-embed"><iframe src="https://www.youtube.com/embed/' + id + '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
                }

                function createBilibiliEmbed(url) {
                    var id = extractBilibiliId(url);
                    if (!id) {
                        return null;
                    }
                    return '<div class="video-embed"><iframe src="https://player.bilibili.com/player.html?bvid=' + id + '&page=1&high_quality=1" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"></iframe></div>';
                }

                function createHtml5Player(url) {
                    return '<div class="video-player"><video controls preload="metadata"><source src="' + url + '" type="video/mp4">您的浏览器不支持 HTML5 视频播放，请<a href="' + url + '" target="_blank">点击下载</a>观看。</video></div>';
                }

                var links = Array.prototype.slice.call(body.querySelectorAll('a'));
                links.forEach(function (link) {
                    var text = link.textContent.trim();
                    if (text.indexOf('▶ 视频：') !== 0) {
                        return;
                    }

                    var url = link.getAttribute('href');
                    if (!url) {
                        return;
                    }

                    var type = detectVideoType(url);
                    var html = null;
                    if (type === 'youtube') {
                        html = createYoutubeEmbed(url);
                    } else if (type === 'bilibili') {
                        html = createBilibiliEmbed(url);
                    } else {
                        html = createHtml5Player(url);
                    }

                    if (!html) {
                        return;
                    }

                    var paragraph = link.closest('p');
                    if (paragraph) {
                        paragraph.className = 'post-content-video';
                        paragraph.innerHTML = html;
                    } else {
                        var wrapper = document.createElement('p');
                        wrapper.className = 'post-content-video';
                        wrapper.innerHTML = html;
                        link.parentNode.replaceChild(wrapper, link);
                    }
                });
            })();
        });
    </script>
@endpush
