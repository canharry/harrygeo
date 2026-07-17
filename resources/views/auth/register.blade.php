@extends('layouts.app')

@section('title', '注册 - 阳光每一天')

@section('content')
    <section class="page-hero" style="--page-hero-color: #ff7eb3;">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <h1 class="post-detail-title">加入阳光每一天</h1>
            <p class="archive-desc">注册账号，开启你的博客之旅</p>
        </div>
    </section>

    <div class="auth-page">
        <div class="auth-box">
            <h2 class="section-title"><i class="bi bi-person-plus"></i> 注册账号</h2>

            <form method="POST" action="{{ route('register') }}" class="comment-form">
                    @csrf

                    <div style="margin-bottom: 18px;">
                        <label for="name" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">昵称</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            class="search-input"
                            style="width: 100%; border-radius: var(--radius-sm);"
                            placeholder="请输入昵称"
                        >
                        @error('name')
                            <span style="display: block; margin-top: 6px; font-size: 0.8rem; color: #e11d48;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label for="email" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">邮箱地址</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="search-input"
                            style="width: 100%; border-radius: var(--radius-sm);"
                            placeholder="请输入邮箱"
                        >
                        @error('email')
                            <span style="display: block; margin-top: 6px; font-size: 0.8rem; color: #e11d48;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label for="password" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">登录密码</label>
                        <div class="password-field">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                class="search-input"
                                style="width: 100%; border-radius: var(--radius-sm);"
                                placeholder="请设置至少 6 位密码"
                            >
                            <button type="button" class="password-toggle" data-target="password" aria-label="显示密码">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span style="display: block; margin-top: 6px; font-size: 0.8rem; color: #e11d48;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin-bottom: 22px;">
                        <label for="password_confirmation" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">确认密码</label>
                        <div class="password-field">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                class="search-input"
                                style="width: 100%; border-radius: var(--radius-sm);"
                                placeholder="请再次输入密码"
                            >
                            <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="显示密码">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="action-btn" style="width: 100%; justify-content: center;">
                        <i class="bi bi-person-plus"></i> 立即注册
                    </button>
                </form>

            <p style="margin-top: 20px; text-align: center; font-size: 0.9rem; color: var(--text-light);">
                已有账号？<a href="{{ route('login') }}" style="color: var(--primary); font-weight: 500;">立即登录</a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            document.querySelectorAll('.password-toggle').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var input = document.getElementById(btn.dataset.target);
                    var icon = btn.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                        btn.setAttribute('aria-label', '隐藏密码');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                        btn.setAttribute('aria-label', '显示密码');
                    }
                });
            });
        })();
    </script>
@endpush
