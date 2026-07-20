<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Post;

$post = Post::first();

if (!$post) {
    echo "No post found\n";
    exit(1);
}

echo "Post ID: {$post->id}, Slug: {$post->slug}\n";

$tableMarkdown = <<<'MD'
<p>下面是文章中的表格示例：</p>

| 功能模块 | 技术栈 | 说明 |
| --- | --- | --- |
| 前端框架 | Laravel Blade + Tailwind CSS | 响应式页面渲染 |
| 后端框架 | Laravel 9 | 业务逻辑与 API |
| 富文本编辑器 | Trix | 文章正文编辑 |
| 表格解析 | league/commonmark | Markdown 表格转 HTML |

<p>表格展示了项目的主要技术栈。</p>
MD;

$post->content = $tableMarkdown;
$post->save();

echo "Post updated successfully\n";
