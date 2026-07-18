@extends('layouts.app')

@section('title', '重置密码 - 阳光每一天')

@section('content')
    <section class="page-hero" style="--page-hero-color: #667eea;">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <h1 class="post-detail-title">重置密码</h1>
            <p class="archive-desc">设置一个新的登录密码</p>
        </div>
    </section>

    <div class="auth-page">
        <div class="auth-box">
            <h2 class="section-title"><i class="bi bi-shield-lock"></i> 设置新密码</h2>

            <form method="POST" action="{{ route('password.update') }}" class="comment-form">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div style="margin-bottom: 18px;">
                    <label for="email" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">邮箱地址</label>
                    <input
                        type="email"
                        id="email"
                        value="{{ $email }}"
                        disabled
                        class="search-input"
                        style="width: 100%; border-radius: var(--radius-sm); background: rgba(138, 138, 163, 0.08);"
                    >
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="password" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">新密码</label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autofocus
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
                    <label for="password_confirmation" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">确认新密码</label>
                    <div class="password-field">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            class="search-input"
                            style="width: 100%; border-radius: var(--radius-sm);"
                            placeholder="请再次输入新密码"
                        >
                        <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="显示密码">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                @error('email')
                    <div style="margin-bottom: 18px; font-size: 0.85rem; color: #e11d48;">
                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror

                <button type="submit" class="action-btn" style="width: 100%; justify-content: center;">
                    <i class="bi bi-check-lg"></i> 确认重置
                </button>
            </form>

            <p style="margin-top: 20px; text-align: center; font-size: 0.9rem; color: var(--text-light);">
                <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 500;">返回登录</a>
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
