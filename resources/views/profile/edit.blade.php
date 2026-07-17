@extends('layouts.app')

@section('title', '个人资料 - 阳光每一天')

@section('content')
    <section class="page-hero" style="--page-hero-color: #667eea;">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <h1 class="post-detail-title">个人资料</h1>
            <p class="archive-desc">更新你的昵称、邮箱与个性签名</p>
        </div>
    </section>

    <div class="auth-page">
        <div class="auth-box">
            <h2 class="section-title"><i class="bi bi-person-gear"></i> 编辑资料</h2>

            @if (session('success'))
                <div style="margin-bottom: 18px; padding: 12px 16px; background: rgba(16, 185, 129, 0.12); color: #047857; border-radius: var(--radius-sm); font-size: 0.9rem;">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="comment-form">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 22px; text-align: center;">
                    <div style="width: 96px; height: 96px; margin: 0 auto 12px; border-radius: 50%; overflow: hidden; border: 4px solid var(--white); box-shadow: 0 4px 16px rgba(0,0,0,0.12); background: linear-gradient(135deg, #e0f7fa, #fce4ec);">
                        @if ($user->avatar)
                            <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="头像预览" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div id="avatarPreview" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-light); font-size: 2rem;">
                                <i class="bi bi-person"></i>
                            </div>
                        @endif
                    </div>
                    <label for="avatar" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 16px; border-radius: 50px; background: rgba(255,255,255,0.6); border: 1px solid rgba(138,138,163,0.25); color: var(--text-main); font-size: 0.85rem; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-camera"></i> 更换头像
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                    @error('avatar')
                        <span style="display: block; margin-top: 8px; font-size: 0.8rem; color: #e11d48;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="name" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">昵称</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="search-input"
                        style="width: 100%; border-radius: var(--radius-sm);"
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
                        value="{{ old('email', $user->email) }}"
                        required
                        class="search-input"
                        style="width: 100%; border-radius: var(--radius-sm);"
                    >
                    @error('email')
                        <span style="display: block; margin-top: 6px; font-size: 0.8rem; color: #e11d48;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 22px;">
                    <label for="signature" style="display: block; margin-bottom: 6px; font-size: 0.9rem; color: var(--text-main);">个性签名</label>
                    <textarea
                        id="signature"
                        name="signature"
                        rows="3"
                        class="search-input"
                        style="width: 100%; border-radius: var(--radius-sm); resize: vertical;"
                        placeholder="写一句个性签名..."
                    >{{ old('signature', $user->signature) }}</textarea>
                    @error('signature')
                        <span style="display: block; margin-top: 6px; font-size: 0.8rem; color: #e11d48;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="action-btn" style="width: 100%; justify-content: center;">
                    <i class="bi bi-check-lg"></i> 保存修改
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var input = document.getElementById('avatar');
            var preview = document.getElementById('avatarPreview');

            if (input && preview) {
                input.addEventListener('change', function () {
                    var file = input.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            if (preview.tagName.toLowerCase() === 'img') {
                                preview.src = e.target.result;
                            } else {
                                var img = document.createElement('img');
                                img.id = 'avatarPreview';
                                img.src = e.target.result;
                                img.alt = '头像预览';
                                img.style.width = '100%';
                                img.style.height = '100%';
                                img.style.objectFit = 'cover';
                                preview.parentNode.replaceChild(img, preview);
                                preview = img;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        })();
    </script>
@endpush
