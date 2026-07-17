# 评论添加图片与表情功能实现计划

## 背景与目标

当前评论区（发表评论、回复评论、编辑评论）仅支持纯文本，用户体验单一。用户希望在评论中插入图片和表情，使互动更加丰富。

本计划目标：
- 在评论框上方增加工具栏（表情 + 图片）。
- 支持插入 Unicode 表情符号。
- 支持上传图片（jpg、jpeg、png、gif、webp，最大 2MB），并在评论内容中安全渲染。
- 适用于主评论表单、回复表单、编辑表单三种场景。

## 方案概述

采用 **占位符 + AJAX 上传** 方案，无需修改数据库表结构：

- 图片上传后存入 `storage/app/public/comments/YYYY/MM/{hash}.{ext}`。
- 在评论 `content` 中以占位符 `[img:comments/YYYY/MM/xxx.jpg]` 表示图片位置。
- 显示时由模型解析占位符，输出安全的 `<img>` 标签；普通文本进行 HTML 转义，防止 XSS。
- 编辑/删除评论时，检测内容中不再被引用的图片文件并清理，避免残留。
- 表情直接以 Unicode 字符插入 `content`，无需特殊处理。

## 关键文件变更

### 1. 路由 `routes/web.php`

新增图片上传路由（需登录）：

```php
Route::post('/comments/upload-image', [App\Http\Controllers\PostController::class, 'uploadCommentImage'])
    ->name('comments.upload-image')
    ->middleware('auth');
```

### 2. 模型 `app/Models/Comment.php`

新增内容解析与图片路径提取方法：

- `parseContent()`：将 `[img:path]` 替换为安全 `<img>` 标签，普通文本转义并保留换行。
- `extractImages()` / `extractImagePaths($content)`：提取内容中所有图片路径。
- `isValidImagePath($path)`：校验路径格式，禁止 `..` 与非法扩展名。
- 在 `booted()` 中监听 `deleting` 事件，兜底清理图片文件。

### 3. 控制器 `app/Http/Controllers/PostController.php`

新增 `use Illuminate\Support\Facades\Storage;`。

- 新增 `uploadCommentImage(Request $request)`：验证并存储图片，返回 `{url, path}` JSON。
- 修改 `updateComment`：更新前提取旧内容图片，更新后提取新内容图片，删除旧内容中不再使用的图片（需确认无其他评论引用）。
- 修改 `destroyComment`：删除评论前提取并清理其引用的图片文件（同样检查是否被其他评论引用）。

### 4. 组件 `resources/views/components/comment-toolbar.blade.php`（新建）

可复用工具栏，包含：

- 表情按钮 `bi-emoji-smile`
- 图片按钮 `bi-image`
- 隐藏的文件输入框 `input[type=file]`
- 表情选择弹窗（30 个常用表情，6 列网格）
- `data-target` 指向对应 textarea 的 id
- `data-upload-url` 指向上传路由

### 5. 视图 `resources/views/posts/show.blade.php`

- 主评论表单 textarea 前插入 `<x-comment-toolbar textarea-id="main-comment-content" />`。
- 通过 `@push('scripts')` 添加页面级 JS：
  - 工具栏按钮点击事件委托。
  - 表情弹窗显示/隐藏。
  - 表情插入光标位置。
  - 图片选择后 AJAX 上传并插入 `[img:path]` 占位符。
  - 上传中按钮禁用并显示 loading 状态。

### 6. 视图 `resources/views/posts/_comment.blade.php`

- 评论内容展示由 `<p>` 改为 `<div class="comment-text">`，输出 `{!! $comment->parseContent() !!}`。
- 回复表单与编辑表单的 textarea 前分别插入工具栏组件，并为 textarea 设置唯一 id：
  - `reply-content-{{ $comment->id }}`
  - `edit-content-{{ $comment->id }}`

### 7. 样式 `resources/css/app.css`

新增以下样式类：

- `.comment-toolbar`：工具栏容器
- `.comment-toolbar-btn` / `.comment-toolbar-btn--emoji` / `.comment-toolbar-btn--image`：工具栏按钮
- `.is-loading`：上传中状态
- `.comment-image-input`：隐藏文件输入
- `.emoji-popup` / `.is-open`：表情弹窗
- `.emoji-grid` / `.emoji-item`：表情网格与单个表情
- `.comment-text`：评论内容容器
- `.comment-inline-image`：评论内联图片

## 安全策略

- 图片路径正则匹配：`comments/YYYY/MM/{hash}.{jpg|jpeg|png|gif|webp}`，禁止 `..`、绝对路径、非法扩展名。
- 渲染时文本段使用 `e()` 转义，图片 `src` 来自受控的 `asset('storage/' . $path)`。
- 上传接口仅允许已登录用户访问，限制文件类型与大小。
- 编辑/删除时清理图片前，先检查是否仍被其他评论引用，避免误删共享图片。

## 表情列表

首批 30 个常用表情，按 6 列展示：

```
😀 😂 🤣 🥰 😍 🤔
😎 😭 😡 😅 😉 😊
🥳 🤩 🙄 😴 🤯 🎉
🔥 ❤️ 💔 🌹 🍺 🎂
🎁 🌟 🌈 👍 👎 🙏
👏
```

## 当前状态

截至本计划生成时，评论图片与表情功能的前后端代码已实现并存在于工作区：

- 路由、模型、控制器逻辑已就位。
- `comment-toolbar` 组件已创建，并已在主评论、回复、编辑三种表单中引用。
- 评论内容渲染已改为 `{!! $comment->parseContent() !!}`，支持 `[img:path]` 占位符与 HTML 安全转义。
- CSS 样式已补充完整。

**剩余工作**：构建前端资源、清理 Laravel 缓存、通过浏览器进行端到端验证。

## 验证步骤

1. 运行 `php artisan route:list` 确认 `comments.upload-image` 路由已注册。
2. 登录后在主评论框点击表情按钮，选择表情，确认插入 textarea 光标处。
3. 点击图片按钮上传 jpg/png/gif/webp（≤2MB），确认 textarea 中出现 `[img:comments/YYYY/MM/xxx.ext]` 占位符。
4. 提交评论，确认页面正确渲染图片，普通文本被转义（可测试 `<script>alert(1)</script>`）。
5. 编辑评论删除图片占位符，保存后确认原图片文件已从 storage 删除（且无其他评论引用）。
6. 删除含图片的评论，确认对应图片文件被清理。
7. 在回复评论和编辑评论表单中重复验证表情与图片功能。
8. 运行 `npm run build` 成功，无编译错误。
9. 执行 `php artisan cache:clear; php artisan view:clear; php artisan config:clear; php artisan route:clear`。
