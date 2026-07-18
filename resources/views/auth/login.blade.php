@extends('layouts.app')

@section('title', '登录 - 阳光每一天')

@section('content')
    <section class="page-hero" style="--page-hero-color: #667eea;">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <h1 class="post-detail-title">欢迎回来</h1>
            <p class="archive-desc">登录后继续浏览和互动</p>
        </div>
    </section>

    <div class="auth-page">
        <div class="auth-box">
                <h2 class="section-title"><i class="bi bi-box-arrow-in-right"></i> 用户登录</h2>

                <form method="POST" action="{{ route('login') }}" class="comment-form">
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
                                placeholder="请输入密码"
                            >
                            <button type="button" class="password-toggle" data-target="password" aria-label="显示密码">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span style="display: block; margin-top: 6px; font-size: 0.8rem; color: #e11d48;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin-bottom: 22px; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--primary);">
                        <label for="remember" style="font-size: 0.9rem; color: var(--text-main);">记住我</label>
                    </div>

                    <button type="submit" class="action-btn" style="width: 100%; justify-content: center;">
                        <i class="bi bi-box-arrow-in-right"></i> 立即登录
                    </button>
                </form>

                <div style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between; font-size: 0.9rem; color: var(--text-light);">
                    <a href="{{ route('password.request') }}" style="color: var(--primary); font-weight: 500;">忘记密码？</a>
                    <span>还没有账号？<a href="{{ route('register') }}" style="color: var(--primary); font-weight: 500;">立即注册</a></span>
                </div>
            </div>
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
