@extends('layouts.app')

@section('title', '忘记密码 - 阳光每一天')

@section('content')
    <section class="page-hero" style="--page-hero-color: #667eea;">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <h1 class="post-detail-title">找回密码</h1>
            <p class="archive-desc">输入注册邮箱，我们将向您发送重置密码链接</p>
        </div>
    </section>

    <div class="auth-page">
        <div class="auth-box">
            <h2 class="section-title"><i class="bi bi-envelope"></i> 发送重置链接</h2>

            @if (session('status'))
                <div style="margin-bottom: 18px; padding: 12px 16px; border-radius: var(--radius-sm); background: rgba(34, 197, 94, 0.12); color: #15803d; font-size: 0.9rem;">
                    <i class="bi bi-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="comment-form">
                @csrf

                <div style="margin-bottom: 18px;">
                    <label for="email" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">邮箱地址</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="search-input"
                        style="width: 100%; border-radius: var(--radius-sm);"
                        placeholder="请输入注册邮箱"
                    >
                    @error('email')
                        <span style="display: block; margin-top: 6px; font-size: 0.8rem; color: #e11d48;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="action-btn" style="width: 100%; justify-content: center;">
                    <i class="bi bi-send"></i> 发送重置链接
                </button>
            </form>

            <p style="margin-top: 20px; text-align: center; font-size: 0.9rem; color: var(--text-light);">
                想起密码了？<a href="{{ route('login') }}" style="color: var(--primary); font-weight: 500;">立即登录</a>
            </p>
        </div>
    </div>
@endsection
