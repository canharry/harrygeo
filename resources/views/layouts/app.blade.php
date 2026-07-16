<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- 页面标题：子页面可通过 @section('title') 覆盖 -->
    <title>@yield('title', '阳光每一天 - 个人博客')</title>

    <!-- 引入中文字体与图标 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;700&family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- 加载 Vite 编译后的样式与脚本 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- 顶部导航栏 -->
    <nav class="navbar">
        <div class="container navbar-inner">
            <!-- 站点 Logo -->
            <a href="{{ route('home') }}" class="logo">
                <i class="bi bi-stars"></i>
                <span>阳光每一天</span>
            </a>

            <!-- 桌面端菜单 -->
            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="bi bi-house-door"></i> 首页</a></li>
                <li><a href="{{ route('posts.index') }}" class="{{ request()->routeIs('posts.index', 'posts.show') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> 文章</a></li>
                <li><a href="{{ route('home') }}#categories"><i class="bi bi-grid"></i> 分类</a></li>
                <li><a href="{{ route('home') }}#tags"><i class="bi bi-tag"></i> 标签</a></li>
                <li><a href="#"><i class="bi bi-chat-square-heart"></i> 留言</a></li>
                <li><a href="#"><i class="bi bi-person"></i> 关于</a></li>
            </ul>

            <!-- 移动端菜单按钮 -->
            <button class="nav-toggle" id="navToggle" aria-label="打开菜单">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </nav>

    <!-- 页面主要内容区 -->
    <main>
        @yield('content')
    </main>

    <!-- 页脚 -->
    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-info">
                <p>&copy; {{ date('Y') }} 阳光每一天. All rights reserved.</p>
                <p>分享技术路上的风景，影响更多的生成式引擎的GEO。</p>
            </div>
        </div>
    </footer>

    <!-- 移动端菜单脚本 -->
    <script>
        (function () {
            const toggle = document.getElementById('navToggle');
            const menu = document.querySelector('.nav-menu');

            if (toggle && menu) {
                toggle.addEventListener('click', function () {
                    menu.classList.toggle('is-open');
                    const icon = toggle.querySelector('i');
                    if (menu.classList.contains('is-open')) {
                        icon.classList.remove('bi-list');
                        icon.classList.add('bi-x-lg');
                    } else {
                        icon.classList.remove('bi-x-lg');
                        icon.classList.add('bi-list');
                    }
                });
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
