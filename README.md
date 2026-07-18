# 阳光每一天 - 个人博客系统

> 一个基于 **Laravel 9 + Filament 2** 构建的中文个人博客平台，支持文章管理、分类标签、评论互动、访问统计、AI 爬虫识别与后台可视化仪表盘。

---

## 目录

1. [项目简介](#项目简介)
2. [技术栈](#技术栈)
3. [主要功能](#主要功能)
4. [项目目录结构](#项目目录结构)
5. [数据库结构](#数据库结构)
6. [模型关系](#模型关系)
7. [路由说明](#路由说明)
8. [中间件说明](#中间件说明)
9. [环境变量说明](#环境变量说明)
10. [安装与运行](#安装与运行)
11. [后台管理](#后台管理)
12. [特色功能说明](#特色功能说明)
13. [开发与扩展](#开发与扩展)
14. [许可证](#许可证)

---

## 项目简介

**阳光每一天** 是一个面向个人博主的内容管理系统（CMS）。项目采用前后端不分离的架构：

- **后端**：Laravel 9 提供路由、控制器、Eloquent ORM、认证、队列、邮件等核心能力。
- **后台 UI**：Filament 2 快速搭建管理后台，提供文章、分类、标签、评论、用户、站点设置等资源的 CRUD。
- **前端**：原生 Blade 模板 + Vite 构建，配合自定义 CSS 实现响应式博客界面。

博客支持多用户投稿：所有注册用户均可进入后台撰写文章，但只能管理自己的文章；管理员拥有全局管理权限。系统内置访问统计、AI 爬虫识别、世界地图与中国地图访问分布、文章排行榜、消息中心等模块，适合作为技术博客、生活博客或 GEO（生成式引擎优化）内容站点。

---

## 技术栈

### 后端

| 技术/框架 | 版本 | 说明 |
|---|---|---|
| PHP | `^8.0.2` | 运行环境 |
| Laravel Framework | `^9.19` | PHP Web 框架，提供 MVC、Eloquent、认证、邮件等 |
| Filament | `2.*` | 基于 Livewire + Tailwind 的后台管理 UI 框架 |
| Laravel Sanctum | `^3.0` | API 认证（预留） |
| Laravel Tinker | `^2.7` | 交互式命令行工具 |
| GuzzleHTTP | `^7.2` | HTTP 客户端，用于 IP 归属地等外部请求 |
| Symfony Process | `6.0.*` | 进程管理 |

### 前端

| 技术/框架 | 版本 | 说明 |
|---|---|---|
| Vite | `^4.0.0` | 前端构建工具 |
| Laravel Vite Plugin | `^0.7.2` | Laravel 与 Vite 的集成插件 |
| jsVectorMap | `^1.7.0` | 后台世界地图与中国地图可视化 |
| Axios | `^1.1.2` | HTTP 请求库 |
| Lodash | `^4.17.19` | 工具库 |
| PostCSS | `^8.1.14` | CSS 处理 |
| Bootstrap Icons | CDN | 图标字体 |
| Google Fonts | CDN | Noto Sans SC + ZCOOL KuaiLe 字体 |

### 数据存储

| 类型 | 说明 |
|---|---|
| MySQL / MariaDB | 主要关系型数据库（通过 `.env` 配置） |
| Redis | 可选缓存/队列驱动 |
| 本地文件系统 | `storage/app/public` 存放上传的图片、视频、头像、封面 |

### 开发工具

| 工具 | 说明 |
|---|---|
| Laravel Pint | `^1.0` 代码格式化 |
| PHPUnit | `^9.5.10` 单元测试 |
| Laravel Sail | `^1.0.1` Docker 开发环境（可选） |
| Spatie Laravel Ignition | `^1.0` 错误页面美化 |
| Faker | `^1.9.1` 测试数据生成 |
| Mockery | `^1.4.4` 测试 Mock |

---

## 主要功能

### 前台功能

- **首页**：展示最新文章、热门推荐、分类列表、标签云、博主信息卡片。
- **文章列表**：按发布时间倒序分页展示所有已发布文章。
- **文章搜索**：支持按标题、摘要、正文、分类名、标签名全文搜索，并高亮关键词。
- **文章详情**：
  - 展示标题、封面图、顶部视频、正文、分类、标签、发布时间、浏览/点赞/评论数。
  - 自动生成文章目录（基于 `h1-h6` 锚点），支持点击平滑滚动。
  - 显示原创/转载徽章、上一篇/下一篇、相关文章推荐。
  - 底部动态显示侵权举报声明（管理员可在后台配置）。
- **分类/标签归档**：按分类或标签筛选文章，支持全局归档和指定用户归档。
- **评论系统**：
  - 登录后可发表评论、回复他人。
  - 评论支持插入图片和表情。
  - 用户可修改/删除自己的评论；一旦有人回复，则锁定不可删改。
  - 评论显示评论者设备类型（手机/电脑）和城市归属地。
- **文章点赞**：基于 IP 限流，24 小时内同一 IP 不重复点赞。
- **用户认证**：登录、注册、退出、忘记密码/重置密码（邮件发送重置链接）。
- **个人资料**：修改昵称、邮箱、个性签名、头像。
- **消息中心**：查看与自己文章/评论相关的互动消息，未读消息在导航栏显示角标。

### 后台功能（Filament）

- **仪表盘**：
  - 每日访问统计卡片（浏览量、阅读量、点赞量、独立访客）。
  - 文章阅读排行榜 TOP10。
  - 文章点赞排行榜 TOP10。
  - 世界地图 / 中国地图访问分布（Tab 切换）。
- **文章管理**：创建/编辑/删除文章，支持分类、标签、封面图、视频、原创/转载、发布状态、浏览量/点赞数（仅管理员可设）。
- **分类管理**：仅管理员可管理分类名称、别名、描述、颜色、图标、排序、显示状态。
- **标签管理**：仅管理员可管理标签名称、别名、颜色。
- **评论管理**：仅管理员可审核/编辑/删除评论。
- **用户管理**：管理员可管理所有用户；普通用户只能查看和编辑自己。
- **站点设置**：管理员可配置站点级文案（如侵权举报邮箱、侵权声明文案）。
- **语言切换**：后台支持中/英文切换。

### 数据统计与 GEO

- **访问跟踪中间件**：自动记录每次页面访问的 IP、国家/地区、文章 ID、访问时间。
- **AI 爬虫识别**：识别 Kimi、DeepSeek、豆包、ChatGPT、Gemini、Claude、文心一言、千问、智谱清言、讯飞星火、天工、MiniMax 等爬虫，并统计各平台对单篇文章的收录次数。
- **每日汇总表**：预聚合每日浏览量、阅读量、点赞数、独立访客数，加速仪表盘查询。
- **IP 归属地**：通过 `ip-api.com` 解析评论者 IP 的城市信息。

---

## 项目目录结构

```
harrygeo/
├── app/                                    # 应用核心代码
│   ├── Console/                            # Artisan 命令
│   ├── Exceptions/                         # 异常处理
│   ├── Filament/                           # Filament 后台相关
│   │   ├── Forms/
│   │   │   └── Components/
│   │   │       └── RichEditor.php          # 自定义富文本编辑器（支持视频上传/链接）
│   │   ├── Pages/
│   │   │   └── Dashboard.php               # 自定义仪表盘页面
│   │   ├── Resources/                      # 后台资源管理
│   │   │   ├── CategoryResource.php
│   │   │   ├── CommentResource.php
│   │   │   ├── PostResource.php
│   │   │   ├── SiteSettingResource.php
│   │   │   ├── TagResource.php
│   │   │   └── UserResource.php
│   │   ├── Resources/*/Pages/              # 各资源的页面类
│   │   │   ├── CategoryResource/Pages/
│   │   │   │   ├── CreateCategory.php
│   │   │   │   ├── EditCategory.php
│   │   │   │   └── ListCategories.php
│   │   │   ├── CommentResource/Pages/
│   │   │   │   ├── CreateComment.php
│   │   │   │   ├── EditComment.php
│   │   │   │   └── ListComments.php
│   │   │   ├── PostResource/Pages/
│   │   │   │   ├── CreatePost.php          # 创建文章，处理 video/slug 等字段
│   │   │   │   ├── EditPost.php            # 编辑文章，处理 video 回填
│   │   │   │   └── ListPosts.php
│   │   │   ├── SiteSettingResource/Pages/
│   │   │   │   └── ManageSiteSettings.php
│   │   │   ├── TagResource/Pages/
│   │   │   │   ├── CreateTag.php
│   │   │   │   ├── EditTag.php
│   │   │   │   └── ListTags.php
│   │   │   └── UserResource/Pages/
│   │   │       ├── CreateUser.php
│   │   │       ├── EditUser.php
│   │   │       └── ListUsers.php
│   │   └── Widgets/                        # 后台仪表盘小部件
│   │       ├── DailyStatsWidget.php
│   │       ├── MapTabsWidget.php
│   │       ├── PostLikeLeaderboardWidget.php
│   │       └── PostReadLeaderboardWidget.php
│   ├── Http/
│   │   ├── Controllers/                    # 控制器
│   │   │   ├── Admin/
│   │   │   │   └── LanguageController.php  # 后台语言切换
│   │   │   ├── AuthController.php          # 登录/注册/退出/密码重置
│   │   │   ├── CategoryController.php      # 分类前台列表与归档
│   │   │   ├── Controller.php              # 基础控制器
│   │   │   ├── HomeController.php          # 首页
│   │   │   ├── MessageController.php       # 消息中心
│   │   │   ├── PostController.php          # 文章/搜索/点赞/评论/上传
│   │   │   ├── ProfileController.php       # 个人资料
│   │   │   ├── TagController.php           # 标签前台列表与归档
│   │   │   └── UserArchiveController.php   # 用户归档页
│   │   ├── Middleware/                     # 中间件
│   │   │   ├── Authenticate.php
│   │   │   ├── EncryptCookies.php
│   │   │   ├── PreventRequestsDuringMaintenance.php
│   │   │   ├── RedirectIfAuthenticated.php
│   │   │   ├── SetLocale.php               # 语言设置
│   │   │   ├── TrackVisit.php              # 访问跟踪与 AI 识别
│   │   │   ├── TrimStrings.php
│   │   │   ├── TrustProxies.php
│   │   │   └── VerifyCsrfToken.php
│   │   └── Kernel.php                      # 中间件注册
│   ├── Models/                             # Eloquent 模型
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   ├── Post.php
│   │   ├── PostAiReference.php
│   │   ├── PostLike.php
│   │   ├── SiteSetting.php
│   │   ├── Tag.php
│   │   ├── User.php
│   │   ├── Visit.php
│   │   └── VisitSummary.php
│   ├── Notifications/                      # 通知类
│   │   └── ResetPasswordNotification.php   # 中文密码重置邮件
│   ├── Providers/                          # 服务提供者
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   └── Services/                           # 业务服务类
│       ├── GeoService.php                  # IP 地理位置（国家/省份）
│       └── IpLocationService.php           # IP 城市解析
├── bootstrap/                              # 应用启动文件
│   ├── app.php
│   └── cache/
├── config/                                 # 配置文件
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── filesystems.php
│   ├── mail.php
│   ├── services.php
│   └── ...                                 # 其他 Laravel 默认配置
├── database/
│   ├── factories/                          # 模型工厂（测试数据）
│   │   ├── CategoryFactory.php
│   │   ├── CommentFactory.php
│   │   ├── PostFactory.php
│   │   ├── PostLikeFactory.php
│   │   ├── TagFactory.php
│   │   ├── UserFactory.php
│   │   ├── VisitFactory.php
│   │   └── VisitSummaryFactory.php
│   ├── migrations/                         # 数据库迁移文件
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2014_10_12_100000_create_password_resets_table.php
│   │   ├── 2019_08_19_000000_create_failed_jobs_table.php
│   │   ├── 2019_12_14_000001_create_personal_access_tokens_table.php
│   │   ├── 2026_07_15_000001_create_categories_table.php
│   │   ├── 2026_07_15_000002_create_tags_table.php
│   │   ├── 2026_07_15_000003_create_posts_table.php
│   │   ├── 2026_07_15_000004_create_post_tag_table.php
│   │   ├── 2026_07_15_000005_create_comments_table.php
│   │   ├── 2026_07_16_074020_add_is_admin_to_users_table.php
│   │   ├── 2026_07_16_093536_create_visits_table.php
│   │   ├── 2026_07_16_093552_create_post_likes_table.php
│   │   ├── 2026_07_16_120000_create_visit_summaries_table.php
│   │   ├── 2026_07_16_150343_add_signature_to_users_table.php
│   │   ├── 2026_07_16_151000_add_region_code_to_visits_table.php
│   │   ├── 2026_07_16_151607_add_avatar_to_users_table.php
│   │   ├── 2026_07_16_154152_add_unique_name_to_tags_table.php
│   │   ├── 2026_07_16_154539_add_unique_name_to_categories_table.php
│   │   ├── 2026_07_16_155017_add_unique_title_to_posts_table.php
│   │   ├── 2026_07_16_155556_change_posts_title_unique_to_user_title.php
│   │   ├── 2026_07_17_063640_create_post_ai_references_table.php
│   │   ├── 2026_07_17_131031_add_ip_and_user_agent_to_comments_table.php
│   │   ├── 2026_07_17_140000_add_is_read_to_comments_table.php
│   │   ├── 2026_07_18_124728_create_site_settings_table.php
│   │   ├── 2026_07_18_124756_add_original_fields_to_posts_table.php
│   │   └── 2026_07_18_210000_add_video_to_posts_table.php
│   └── seeders/
│       └── DatabaseSeeder.php              # 默认生成用户、分类、标签、文章、访问/点赞数据
├── lang/                                   # 语言包
│   ├── en/                                 # 英文语言文件
│   └── zh_CN/                              # 中文语言文件
│       ├── auth.php
│       ├── pagination.php
│       ├── passwords.php
│       └── validation.php
├── public/                                 # Web 入口与静态资源
│   ├── index.php                           # 入口文件
│   ├── build/                              # Vite 编译产物
│   ├── css/
│   │   └── filament-custom.css             # Filament 自定义样式
│   ├── favicon.ico
│   ├── robots.txt
│   └── storage/                            # 上传文件符号链接
├── resources/
│   ├── css/
│   │   └── app.css                         # 前台主题样式
│   ├── js/
│   │   └── app.js                          # 前台交互脚本（花瓣动画、点赞等）
│   └── views/                              # Blade 视图
│       ├── auth/                           # 认证相关视图
│       │   ├── forgot-password.blade.php
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── reset-password.blade.php
│       ├── categories/                     # 分类列表与归档
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── components/                     # Blade 组件
│       │   ├── comment-toolbar.blade.php
│       │   └── image-placeholder.blade.php
│       ├── filament/                       # Filament 自定义视图
│       │   ├── language-switcher.blade.php
│       │   ├── pages/
│       │   │   └── system.blade.php
│       │   └── widgets/
│       │       ├── map-tabs.blade.php
│       │       └── _jsvectormap-assets.blade.php
│       ├── home/                           # 首页
│       │   └── index.blade.php
│       ├── layouts/                        # 布局模板
│       │   └── app.blade.php
│       ├── messages/                       # 消息中心
│       │   └── index.blade.php
│       ├── pagination/                     # 自定义分页样式
│       │   └── custom.blade.php
│       ├── posts/                          # 文章列表、详情、搜索
│       │   ├── index.blade.php
│       │   ├── search.blade.php
│       │   ├── show.blade.php
│       │   └── _comment.blade.php
│       ├── profile/                        # 个人资料
│       │   └── edit.blade.php
│       ├── tags/                           # 标签列表与归档
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── users/                          # 用户归档页
│       │   ├── categories.blade.php
│       │   └── tags.blade.php
│       ├── vendor/                         # 覆盖第三方包视图
│       │   └── forms/
│       │       ├── component-container.blade.php
│       │       └── components/
│       │           ├── actions/
│       │           ├── builder/
│       │           ├── dropdown/
│       │           ├── field-wrapper/
│       │           ├── markdown-editor/
│       │           ├── modal/
│       │           ├── rich-editor/        # 覆盖 Trix 富文本编辑器
│       │           │   └── toolbar-button.blade.php
│       │           ├── tabs/
│       │           ├── wizard/
│       │           └── ...                 # 其他表单组件覆盖
│       └── welcome.blade.php
├── routes/
│   ├── web.php                             # Web 路由
│   ├── api.php                             # API 路由（预留）
│   └── console.php                         # Console 路由
├── storage/                                # 存储日志、缓存、上传文件
│   ├── app/public/                         # 用户上传文件
│   │   ├── covers/                         # 文章封面图
│   │   ├── content-videos/                 # 正文上传视频
│   │   └── ...
│   ├── framework/                          # 框架缓存、会话等
│   └── logs/                               # 应用日志
├── tests/                                  # 测试用例
│   ├── Feature/
│   └── Unit/
├── .editorconfig
├── .env / .env.example                     # 环境配置
├── .gitattributes
├── .gitignore
├── artisan                                 # Artisan 命令入口
├── composer.json                           # PHP 依赖
├── composer.lock
├── Function.md                             # 开发任务备忘
├── package.json                            # Node 依赖
├── package-lock.json
├── phpunit.xml                             # PHPUnit 配置
├── README.md                               # 本文件
└── vite.config.js                          # Vite 配置
```

---

## 数据库结构

### 核心表

| 表名 | 说明 | 关键字段 |
|---|---|---|
| `users` | 用户表 | `name`, `email`, `password`, `is_admin`, `signature`, `avatar` |
| `posts` | 文章表 | `category_id`, `user_id`, `title`, `slug`, `summary`, `content`, `cover_image`, `video`, `views`, `likes`, `is_published`, `is_original`, `original_url`, `published_at` |
| `categories` | 分类表 | `name`, `slug`, `description`, `icon`, `color`, `sort_order`, `is_show` |
| `tags` | 标签表 | `name`, `slug`, `color` |
| `post_tag` | 文章-标签多对多关联表 | `post_id`, `tag_id` |
| `comments` | 评论表 | `post_id`, `user_id`, `parent_id`, `content`, `ip_address`, `user_agent`, `is_read` |

### 统计与 GEO 表

| 表名 | 说明 |
|---|---|
| `visits` | 每次页面访问记录 |
| `visit_summaries` | 每日访问汇总 |
| `post_likes` | 每次点赞记录 |
| `post_ai_references` | AI 平台对文章的收录次数 |

### 系统表

| 表名 | 说明 |
|---|---|
| `password_resets` | 密码重置令牌 |
| `site_settings` | 站点动态配置 |
| `failed_jobs` | 失败队列任务 |
| `personal_access_tokens` | Sanctum Token |

### 索引与约束

- `tags.name` 全局唯一
- `categories.name` 全局唯一
- `posts.title` 按用户唯一（`user_id + title` 联合唯一）
- `posts.slug` 全局唯一

---

## 模型关系

```
User
├── hasMany Post
├── hasMany Comment
└── hasMany Message（通过评论/回复间接生成）

Post
├── belongsTo User
├── belongsTo Category
├── belongsToMany Tag
├── hasMany Comment
├── hasMany PostLike
└── hasMany PostAiReference

Category
└── hasMany Post

Tag
└── belongsToMany Post

Comment
├── belongsTo Post
├── belongsTo User
├── belongsTo Comment（parent，回复）
└── hasMany Comment（replies）
```

---

## 路由说明

### 前台公开路由

| 方法 | 路由 | 控制器 | 名称 | 说明 |
|---|---|---|---|---|
| GET | `/` | `HomeController@index` | `home` | 首页 |
| GET | `/posts` | `PostController@index` | `posts.index` | 文章列表 |
| GET | `/search` | `PostController@search` | `posts.search` | 文章搜索 |
| GET | `/posts/{slug}` | `PostController@show` | `posts.show` | 文章详情 |
| POST | `/posts/{slug}/like` | `PostController@like` | `posts.like` | 文章点赞 |
| GET | `/categories` | `CategoryController@index` | `categories.index` | 分类列表 |
| GET | `/categories/{slug}` | `CategoryController@show` | `categories.show` | 分类归档 |
| GET | `/tags` | `TagController@index` | `tags.index` | 标签列表 |
| GET | `/tags/{slug}` | `TagController@show` | `tags.show` | 标签归档 |
| GET | `/users/{user}/categories` | `UserArchiveController@categories` | `users.categories` | 用户分类归档 |
| GET | `/users/{user}/tags` | `UserArchiveController@tags` | `users.tags` | 用户标签归档 |

### 认证路由（`guest` 中间件）

| 方法 | 路由 | 名称 | 说明 |
|---|---|---|---|
| GET/POST | `/login` | `login` | 登录 |
| GET/POST | `/register` | `register` | 注册 |
| GET/POST | `/forgot-password` | `password.request` | 忘记密码（限流 5 次/分钟） |
| GET | `/reset-password/{token}` | `password.reset` | 重置密码表单 |
| POST | `/reset-password` | `password.update` | 提交重置密码（限流 5 次/分钟） |

### 登录后路由（`auth` 中间件）

| 方法 | 路由 | 名称 | 说明 |
|---|---|---|---|
| POST | `/logout` | `logout` | 退出登录 |
| GET/PUT | `/profile` | `profile.edit` / `profile.update` | 个人资料编辑/更新 |
| GET | `/messages` | `messages.index` | 消息中心 |
| POST | `/posts/{slug}/comments` | `posts.comments.store` | 发表评论 |
| DELETE | `/posts/{slug}/comments/{comment}` | `posts.comments.destroy` | 删除评论 |
| PUT | `/posts/{slug}/comments/{comment}` | `posts.comments.update` | 修改评论 |
| POST | `/comments/upload-image` | `comments.upload-image` | 上传评论图片 |
| POST | `/posts/upload-video` | `posts.upload-video` | 上传正文视频 |

### 后台路由

Filament 后台默认挂载在 `/admin`，由 Filament 自动注册。

| 路由 | 说明 |
|---|---|
| `/admin` | 后台仪表盘 |
| `/admin/posts` | 文章管理 |
| `/admin/categories` | 分类管理（管理员） |
| `/admin/tags` | 标签管理（管理员） |
| `/admin/comments` | 评论管理（管理员） |
| `/admin/users` | 用户管理 |
| `/admin/site-settings` | 站点设置（管理员） |

---

## 中间件说明

| 中间件 | 注册位置 | 说明 |
|---|---|---|
| `SetLocale` | `web` 中间件组 | 从 Session 或浏览器首选语言设置应用语言（`zh_CN`/`en`） |
| `TrackVisit` | `web` 中间件组 | 在响应发送后记录访问日志、识别 AI 爬虫、更新每日汇总 |
| `Authenticate` | 路由中间件 `auth` | 验证用户是否登录 |
| `RedirectIfAuthenticated` | 路由中间件 `guest` | 已登录用户重定向到首页 |
| `ThrottleRequests` | 路由中间件 `throttle` | 限流，用于密码重置等接口 |

---

## 环境变量说明

复制 `.env.example` 为 `.env` 后，主要需要配置的变量：

```dotenv
# 应用基础配置
APP_NAME=阳光每一天
APP_ENV=local
APP_KEY=                              # 运行 php artisan key:generate 生成
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# 数据库
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=harrygeo
DB_USERNAME=root
DB_PASSWORD=your_password

# 缓存/会话/队列（开发环境可保持 file/sync）
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# 邮件（密码重置需要）
# 本地测试可改为 log，邮件内容会写入 storage/logs/laravel.log
MAIL_MAILER=log
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Redis（可选）
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 安装与运行

### 环境要求

- PHP >= 8.0.2
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Node.js 16+ 与 npm（用于前端构建）

### 安装步骤

```bash
# 1. 克隆项目并进入目录
cd harrygeo

# 2. 安装 PHP 依赖
composer install

# 3. 复制环境配置文件
cp .env.example .env

# 4. 生成应用密钥
php artisan key:generate

# 5. 配置数据库（编辑 .env）
# DB_DATABASE=harrygeo
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 6. 运行迁移并填充演示数据
php artisan migrate --seed

# 7. 创建上传目录符号链接
php artisan storage:link

# 8. 安装前端依赖并构建
npm install
npm run build

# 9. 启动本地开发服务器
php artisan serve
```

访问 `http://127.0.0.1:8000` 即可查看前台，后台地址为 `http://127.0.0.1:8000/admin`。

### 默认账号

迁移填充后会创建一个默认管理员账号：

- 邮箱：`admin@example.com`
- 密码：`password`

> 生产环境请务必修改默认密码。

### 前端开发模式

开发时可以使用 Vite 热更新：

```bash
npm run dev
```

生产环境或部署前执行：

```bash
npm run build
```

---

## 后台管理

后台基于 Filament 2 构建，访问 `/admin` 并使用已登录账号进入。

### 权限设计

| 角色 | 权限说明 |
|---|---|
| 管理员（`is_admin = true`） | 可管理所有文章、分类、标签、评论、用户、站点设置，查看全局统计数据 |
| 普通用户（`is_admin = false`） | 可进入后台写文章/管理自己的文章，查看与自己文章相关的统计数据和消息 |

### 后台菜单

- **仪表盘**：每日统计、排行榜、地图分布
- **文章**：创建/编辑/删除文章
- **分类**：仅管理员
- **标签**：仅管理员
- **评论**：仅管理员
- **用户**：管理员可管理全部，普通用户只能看自己
- **站点设置**：仅管理员

---

## 特色功能说明

### 1. 文章编辑器与多媒体

- 使用自定义 `RichEditor` 组件，基于 Trix 扩展。
- 支持：粗体、斜体、下划线、删除线、链接、标题、引用、代码块、有序/无序列表、图片上传、视频上传、视频链接。
- 编辑器最小高度 `700px`，最大高度 `1800px`，支持垂直滚动和手动拖拽调整大小。
- 文章顶部可单独上传视频文件或填写 YouTube/Bilibili 外部链接，本地视频优先。

### 2. 原创 / 转载标识

- 文章可选择“原创”或“转载”。
- 转载文章必须填写原文链接，详情页会显示“转载文章”徽章和“查看原文”按钮。
- 原创文章显示“原创文章”徽章。

### 3. 封面图策略

- 支持本地上传（`storage/app/public/covers`）或外部 URL。
- 本地上传优先于外部 URL。
- 两者都为空时，前台使用渐变占位图，不影响阅读体验。

### 4. 评论系统

- 登录用户可发表评论和回复。
- 评论支持插入图片（`[img:path]` 占位符），自动渲染为安全图片标签。
- 评论可修改/删除，但一旦被他人回复则锁定。
- 评论显示评论者设备类型（手机/电脑）和城市归属地。

### 5. 消息中心

- 当其他用户评论了自己的文章或回复了自己的评论时，会生成未读消息。
- 导航栏显示未读消息数量角标。
- 进入消息中心后自动标记相关消息为已读。

### 6. AI 爬虫识别

- `TrackVisit` 中间件通过 User-Agent 识别主流 AI 爬虫。
- 识别到爬虫访问文章详情页时，累加 `post_ai_references` 表的收录计数。
- 后台文章列表可查看各文章的 AI 收录汇总。

### 7. 访问统计地图

- 使用 `jsVectorMap` 在后台仪表盘展示。
- 世界地图和中国地图通过 Tab 切换，避免页面过长。
- 普通用户只能看到自己文章读者的分布，管理员查看全站分布。

### 8. 站点设置

- 管理员可在后台维护 `site_settings` 表。
- 目前用于配置侵权举报邮箱和侵权声明文案，动态注入文章详情页底部。
- 支持 HTML 内容，设置变更后自动清理缓存。

### 9. 多语言

- 支持中文（`zh_CN`）和英文（`en`）。
- 通过 `SetLocale` 中间件从 Session 或浏览器首选语言自动切换。
- 后台顶部用户菜单提供手动切换入口。

---

## 开发与扩展

### 代码风格

项目使用 Laravel Pint 进行代码格式化：

```bash
./vendor/bin/pint
```

### 运行测试

```bash
php artisan test
```

### 常用 Artisan 命令

```bash
# 刷新数据库并填充数据
php artisan migrate:fresh --seed

# 清除缓存
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 创建上传目录符号链接
php artisan storage:link

# 进入 Tinker 交互式命令行
php artisan tinker
```

### 扩展建议

1. **真实 GEO 服务**：将 `GeoService` 替换为 MaxMind GeoIP2 或 ip-api.com 商业版，获取真实国家/省份信息。
2. **队列处理**：将访问统计、邮件发送等操作改为队列异步处理，提升响应速度。
3. **SEO 优化**：为文章详情页添加更多 meta 标签、结构化数据和面包屑导航。
4. **图片处理**：集成 Intervention Image 对上传图片进行压缩和水印处理。
5. **全文搜索**：引入 Laravel Scout + Elasticsearch/Meilisearch 替代 LIKE 搜索。
6. **API 完善**：基于已有的 Sanctum 配置，开发移动端或小程序 API。

---

## 许可证

本项目基于 Laravel 框架构建，遵循 [MIT license](https://opensource.org/licenses/MIT)。
