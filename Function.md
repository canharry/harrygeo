## 1. 完成首页先把数据跑起来（最优先）
- 运行 php artisan migrate --seed
- 这样首页的文章、分类、标签、统计数据才会正常显示
- 如果还没有 PHP 环境，先安装 PHP 8.0.2+ 和 Composer
## 2. 文章详情页
- 创建 PostController@show
- 设计 resources/views/posts/show.blade.php
- 路由： /posts/{slug} 或 /article/{slug}
- 展示：标题、封面、正文、分类、标签、发布时间、浏览/点赞/评论数
## 3. 分类页与标签页
- /categories/{slug} ：按分类展示文章列表
- /tags/{slug} ：按标签展示文章列表
- 这两个页面布局可以直接复用首页右侧的文章网格
## 4. 搜索功能
- 在左侧边栏的搜索框接入后端
- 实现按标题、摘要、内容的关键字搜索
- 路由： /search?q=关键词
## 5. 评论系统
- 完善 CommentController
- 文章详情页底部展示评论列表
- 支持匿名评论或登录后评论（看你需求）
## 6. 后台管理
- 用 Laravel 自带认证或自行实现登录
- 管理文章、分类、标签的增删改查
- 推荐先用原生 Blade + 表单，后续再考虑 Vue/React 后台
## 7. 优化与上线
- SEO：设置 title、description、面包屑导航
- 图片上传：用 Storage 保存封面图，替换生成图链接
- 分页样式美化、加载动画
- 部署到服务器