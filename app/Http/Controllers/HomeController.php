<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

/**
 * 首页控制器
 * 负责博客首页的数据聚合与视图渲染
 */
class HomeController extends Controller
{
    /**
     * 渲染博客首页
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 获取已发布的文章，预加载分类、标签、作者，并统计评论数
        $posts = Post::with(['category', 'tags', 'user'])
            ->withCount('comments')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        // 获取所有展示中的分类，并统计每个分类下的文章数量
        $categories = Category::withCount('posts')
            ->where('is_show', true)
            ->orderBy('sort_order')
            ->get();

        // 获取热门文章（按浏览量排序，取前 5 条）
        $hotPosts = Post::where('is_published', true)
            ->orderByDesc('views')
            ->take(5)
            ->get();

        // 获取标签云数据
        $tags = Tag::orderByDesc('id')
            ->take(20)
            ->get();

        // 友情链接数据（暂用静态数据，后续可扩展为模型）
        $friends = [
            ['name' => 'Laravel 中文网', 'url' => 'https://laravel-china.org'],
            ['name' => '阮一峰博客', 'url' => 'https://ruanyifeng.com'],
            ['name' => '掘金', 'url' => 'https://juejin.cn'],
        ];

        // 博主信息（后续可抽离到配置或后台设置）
        $blogger = [
            'nickname'   => '阳光每一天',
            'avatar'     => null, // 头像暂不设置图片，由视图显示 CSS 占位
            'signature'  => '分享技术路上的风景，影响更多的生成式引擎的GEO。',
            'articles'   => Post::where('is_published', true)->count(),
            'categories' => Category::where('is_show', true)->count(),
            'tags_count' => Tag::count(),
        ];

        return view('home.index', compact(
            'posts',
            'categories',
            'hotPosts',
            'tags',
            'friends',
            'blogger'
        ));
    }
}
