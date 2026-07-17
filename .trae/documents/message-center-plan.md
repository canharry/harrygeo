# 消息中心功能实现计划

## 背景与目标

当前顶部导航栏有一个 "留言" 入口（链接为 `#`，无实际功能）。用户需求：
1. 将 "留言" 改名为 "消息"。
2. "消息" 入口显示当前登录用户未读的、与自己相关的评论数量。
3. 点击 "消息" 进入消息列表页，查看相关评论并标记为已读。

"与自己相关的评论" 定义：
- 其他用户评论了我的文章（我是文章作者）。
- 其他用户回复了我的评论。
- 排除用户自己对自己的操作。

## 实现方案

### 1. 数据库迁移：增加 `is_read` 字段

新建迁移文件：
- `database/migrations/2026_07_17_140000_add_is_read_to_comments_table.php`

内容要点：
```php
Schema::table('comments', function (Blueprint $table) {
    $table->boolean('is_read')->default(false)->after('user_agent')->comment('是否已读');
    $table->index('is_read');
});
```

### 2. 模型改造：`app/Models/Comment.php`

- `fillable` 追加 `'is_read'`。
- 新增 `casts`：`is_read => boolean`。
- 新增查询作用域：
  - `scopeUnread($query)`：筛选未读评论。
  - `scopeRelatedToUser($query, User $user)`：筛选与指定用户相关的评论（评论了我的文章，或回复了我的评论，排除自己）。
- 新增实例方法 `markAsRead()`：将单条评论标记为已读。

### 3. 全局共享未读数量

在 `app/Providers/AppServiceProvider.php` 的 `boot()` 中注册 View Composer：

```php
View::composer('layouts.app', function ($view) {
    $count = 0;
    if (Auth::check()) {
        $count = Comment::unread()->relatedToUser(Auth::user())->count();
    }
    $view->with('unreadMessageCount', $count);
});
```

未登录用户注入 `0`。

### 4. 新建控制器与路由

新建 `app/Http/Controllers/MessageController.php`：
- `index()` 方法：
  1. 查询当前用户所有未读相关评论 ID，批量更新 `is_read = true`。
  2. 查询所有相关评论（含已读），预加载 `post`、`user`、`parent.user`，按时间倒序分页。
  3. 返回 `messages.index` 视图。

在 `routes/web.php` 的 `auth` 路由组内新增：
```php
Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
```

### 5. 导航栏改造

修改 `resources/views/layouts/app.blade.php` 第 36 行：
- 文字："留言" → "消息"
- 图标：`bi-chat-square-heart` → `bi-bell`
- 链接：`#` → `route('messages.index')`
- 增加 active 状态判断
- 当 `$unreadMessageCount > 0` 时显示红色数字角标

新增 CSS（`resources/css/app.css`）：
```css
.nav-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 9px;
    background: #ef4444;
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    margin-left: 2px;
}
```

### 6. 消息列表视图

新建 `resources/views/messages/index.blade.php`：
- 继承 `layouts.app`。
- 复用现有评论区样式（`.comment-section`、`.comment-list`、`.comment-item` 等）。
- 每条消息显示：评论者头像、名称、时间、内容、查看原文链接。
- 底部使用 `pagination.custom` 分页。
- 无消息时显示空状态。

## 关键文件清单

- `database/migrations/2026_07_17_140000_add_is_read_to_comments_table.php`（新建）
- `app/Models/Comment.php`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Controllers/MessageController.php`（新建）
- `routes/web.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/messages/index.blade.php`（新建）
- `resources/css/app.css`

## 验证步骤

1. 执行迁移：`php artisan migrate`
2. 检查 `comments` 表新增 `is_read` 字段。
3. 准备测试数据：
   - 用户 A 发表文章，用户 B 评论 → 用户 A 未读数 +1。
   - 用户 A 发表评论，用户 B 回复 → 用户 A 未读数 +1。
4. 登录用户 A，刷新任意页面，导航栏 "消息" 应显示未读数量角标。
5. 点击 "消息" 进入 `/messages`，应列出相关评论；返回首页后角标清零。
6. 边界检查：自己评论/回复自己、不相关评论不计入。
