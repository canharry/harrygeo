# SEO 优化实施计划

## 上下文

用户希望在文章详情页实现 README 中提到的 SEO 扩展建议：添加更多 meta 标签、结构化数据（Schema.org）和面包屑导航。当前项目已具备基础的页面标题覆盖能力（`@yield('title')`），但缺少 `description`、`keywords`、Open Graph、Twitter Card、JSON-LD 结构化数据以及符合 Schema.org 标准的面包屑导航。本计划旨在不破坏现有布局与功能的前提下，为文章详情页补充这些 SEO 元素，并为后续扩展到其他页面预留统一的注入接口。

## 目标

1. 在文章详情页注入完整的 `<head>` SEO 元信息。
2. 添加 JSON-LD 结构化数据：`Article` 和 `BreadcrumbList`。
3. 完善页面内的可视化面包屑导航，与现有 Hero 区域风格保持一致。
4. 在布局层提供可复用的 SEO 注入机制，方便其他页面复用。

## 实施方案

### 1. 布局层扩展（`resources/views/layouts/app.blade.php`）

在 `<head>` 中补充以下可覆盖/可推送的区域：

- `@yield('meta_description', '默认站点描述')`
- `@yield('meta_keywords', '默认关键词')`
- `@yield('canonical', request()->url())` — 规范链接
- `@stack('meta')` — 用于 Open Graph / Twitter Card 等自定义 meta
- `@stack('structured_data')` — 用于 JSON-LD 脚本

同时添加全局默认的 `description` 和 `keywords`，并在没有子页面覆盖时使用站点级默认值。

### 2. 文章详情页 SEO 注入（`resources/views/posts/show.blade.php`）

在 `@section('title', ...)` 下方追加：

- `@section('meta_description', $post->summary ?: Str::limit(strip_tags($post->content), 150))`
- `@section('meta_keywords', $post->tags->pluck('name')->implode(','))`
- `@section('canonical', route('posts.show', $post->slug))`

通过 `@push('meta')` 注入 Open Graph 和 Twitter Card：

- `og:title` = 文章标题
- `og:description` = 文章摘要
- `og:type` = `article`
- `og:url` = 文章 canonical URL
- `og:image` = 封面图 URL（存在时）
- `og:site_name` = 站点名称
- `og:locale` = `zh_CN`
- `article:published_time` / `article:modified_time` / `article:section` / `article:tag`
- `twitter:card` = `summary_large_image`（有封面图时）或 `summary`
- `twitter:title` / `twitter:description` / `twitter:image`

通过 `@push('structured_data')` 注入两段 JSON-LD：

1. **Article Schema**：包含 `@context`、`@type`、`headline`、`description`、`image`、`author`、`publisher`、`datePublished`、`dateModified`、`mainEntityOfPage`。
2. **BreadcrumbList Schema**：包含首页 → 分类 → 文章正文三级路径。

所有动态文本通过 `e()` 或 Blade 自动转义，避免 JSON/LD 和 HTML 注入风险；日期使用 ISO 8601 格式。

### 3. 面包屑导航增强

当前 `show.blade.php` Hero 区域已有简单的面包屑：

```
首页 > 分类名 > 正文
```

保持该视觉结构，但做以下增强：

- 添加 `aria-label="breadcrumb"` 和 `itemscope itemtype="https://schema.org/BreadcrumbList"` 等 Schema 属性（或仅通过 JSON-LD 提供结构化数据，避免内联 Microdata 与 JSON-LD 重复）。
- 保留现有 `.post-breadcrumb` 样式，不新增复杂样式。
- 由于结构化数据已通过 JSON-LD 提供，页面内面包屑以视觉和可访问性为主，不强制使用 Microdata。

### 4. 控制器层（可选，但推荐）

`PostController::show()` 已传递 `$post`、`$blogger`、`$infringementNotice` 等数据，无需新增数据库查询。视图层可直接基于 `$post` 生成 SEO 数据，因此**不修改控制器**，保持职责单一。

### 5. CSS 微调（`resources/css/app.css`）

现有 `.post-breadcrumb` 样式已满足视觉需求，无需大改。可考虑追加：

- `.post-breadcrumb [aria-current="page"]` 的当前页样式（如颜色加深、取消下划线），提升可访问性。
- 如需要，为 JSON-LD 脚本添加 `display: none` 兜底（通常浏览器默认不显示，但可显式声明）。

## 修改文件清单

| 文件 | 修改内容 |
|---|---|
| `resources/views/layouts/app.blade.php` | 扩展 `<head>`，增加 meta description/keywords/canonical 及 `@stack('meta')`、`@stack('structured_data')` |
| `resources/views/posts/show.blade.php` | 注入文章页 meta、Open Graph、Twitter Card、JSON-LD Article、JSON-LD BreadcrumbList |
| `resources/css/app.css` | 微调面包屑当前页样式（可选） |

## 验证方案

1. **本地访问文章详情页**：启动 `php artisan serve`，打开任意文章页。
2. **查看源码**：右键查看页面源代码，确认：
   - `<title>`、`<meta name="description">`、`<meta name="keywords">`、`<link rel="canonical">` 存在且内容正确。
   - Open Graph 标签（`og:*`）完整。
   - Twitter Card 标签（`twitter:*`）完整。
   - 页面底部或 `<head>` 中包含两段 `<script type="application/ld+json">`。
3. **使用在线工具检测**：
   - Google 富媒体搜索结果测试（Rich Results Test）验证 `Article` 和 `BreadcrumbList`。
   - Facebook Sharing Debugger 验证 Open Graph。
   - Twitter Card Validator 验证 Twitter Card。
4. **检查无报错**：确保页面正常渲染，无 Blade 语法错误或 JSON 转义问题。
5. **运行构建**：执行 `npm run build`，确认前端资源编译正常。

## 注意事项

- 所有动态输出使用 Blade 自动转义或 `e()`，避免 XSS。
- JSON-LD 中字符串使用双引号，需确保 Blade 输出不会破坏 JSON 结构。
- `og:image` 和 `twitter:image` 仅在 `$post->cover_image` 存在时输出，避免空图片标签。
- 摘要为空时，从正文中提取前 150 个字符作为 fallback。
- 保持现有页面结构和样式不变，SEO 注入为纯增量修改。
