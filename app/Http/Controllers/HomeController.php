<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FriendshipLink;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // 友情链接数据（从后台管理的数据库中读取）
        $friends = FriendshipLink::shown()
            ->get()
            ->map(fn (FriendshipLink $link) => ['name' => $link->name, 'url' => $link->url])
            ->all();

        // 博主信息：登录后显示当前用户的信息与统计
        $user = Auth::user();
        $defaultSignature = '分享技术路上的风景，影响更多的生成式引擎的GEO。';

        if ($user) {
            $userPublishedPosts = $user->posts()->where('is_published', true);
            $articlesCount = $userPublishedPosts->count();
            $categoriesCount = $userPublishedPosts->clone()->distinct('category_id')->count('category_id');
            $tagsCount = Tag::whereHas('posts', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_published', true);
            })->count();
        } else {
            $articlesCount = Post::where('is_published', true)->count();
            $categoriesCount = Category::where('is_show', true)->count();
            $tagsCount = Tag::count();
        }

        $blogger = [
            'user_id'    => $user?->id,
            'nickname'   => $user ? $user->name : '阳光每一天',
            'avatar'     => $user?->avatar ? asset('storage/' . $user->avatar) : null, // 头像暂不设置图片，由视图显示 CSS 占位
            'signature'  => $user?->signature ?? $defaultSignature,
            'articles'   => $articlesCount,
            'categories' => $categoriesCount,
            'tags_count' => $tagsCount,
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
