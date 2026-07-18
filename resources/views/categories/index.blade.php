@extends('layouts.app')

@section('title', '全部分类 - 阳光每一天')

@section('content')
    <!-- 分类列表页 Hero 小横幅 -->
    <section class="page-hero">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="post-breadcrumb">
                <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> 首页</a>
                <i class="bi bi-chevron-right"></i>
                <span>全部分类</span>
            </div>
            <h1 class="archive-title"><i class="bi bi-grid-3x3-gap"></i> 全部分类</h1>
            <p class="archive-desc">共 {{ $categories->count() }} 个分类</p>
        </div>
    </section>

    <div class="container main-layout">
        <aside class="sidebar">
            <div class="card">
                <h3 class="card-title"><i class="bi bi-info-circle"></i> 提示</h3>
                <p class="empty-tip">点击分类可查看该分类下的所有文章。</p>
            </div>
        </aside>

        <section class="content-area">
            @if ($categories->count())
                <div class="category-grid">
                    @foreach ($categories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="category-card-item" style="--cat-color: {{ $category->color ?? '#ff7eb3' }}">
                            <i class="bi {{ $category->icon ?? 'bi-folder' }}"></i>
                            <h3>{{ $category->name }}</h3>
                            <span>{{ $category->posts_count }} 篇文章</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-card">
                    <i class="bi bi-inbox"></i>
                    <p>暂无分类</p>
                </div>
            @endif
        </section>
    </div>
@endsection
