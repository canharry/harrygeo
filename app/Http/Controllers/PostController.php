<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\Tag;
use App\Models\VisitSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 文章控制器
 * 负责文章详情页等文章相关页面
 */
class PostController extends Controller
{
    /**
     * 文章列表页
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 获取已发布文章，按发布时间倒序分页
        $posts = Post::with(['category', 'tags', 'user'])
            ->withCount('comments')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('posts.index', compact('posts'));
    }

    /**
     * 文章详情页
     *
     * @param string $slug 文章 URL 标识
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // 根据 slug 查询已发布文章，并预加载分类、标签、作者及评论
        $post = Post::with(['category', 'tags', 'user', 'comments.user', 'aiReferences'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // 增加浏览量（简单实现，每次访问 +1）
        $post->increment('views');

        // 上一篇 / 下一篇文章（按发布时间）
        $prevPost = Post::where('is_published', true)
            ->where('published_at', '<', $post->published_at)
            ->orderByDesc('published_at')
            ->first();

        $nextPost = Post::where('is_published', true)
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at')
            ->first();

        // 相关文章：同分类下的其他文章
        $relatedPosts = Post::where('is_published', true)
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->take(4)
            ->get();

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
            $categoriesCount = \App\Models\Category::where('is_show', true)->count();
            $tagsCount = Tag::count();
        }

        $blogger = [
            'nickname'   => $user ? $user->name : '阳光每一天',
            'avatar'     => $user?->avatar ? asset('storage/' . $user->avatar) : null,
            'signature'  => $user?->signature ?? $defaultSignature,
            'articles'   => $articlesCount,
            'categories' => $categoriesCount,
            'tags_count' => $tagsCount,
        ];

        return view('posts.show', compact('post', 'prevPost', 'nextPost', 'relatedPosts', 'blogger'));
    }

    /**
     * 文章点赞
     *
     * @param string $slug 文章 URL 标识
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $ip = request()->ip() ?? '127.0.0.1';

        // 同一 IP 24 小时内不重复点赞
        $alreadyLiked = PostLike::where('post_id', $post->id)
            ->where('ip_address', $ip)
            ->where('liked_at', '>=', now()->subDay())
            ->exists();

        if ($alreadyLiked) {
            return response()->json([
                'success' => false,
                'message' => '您已经点过赞了',
                'likes'   => $post->likes,
            ]);
        }

        // 记录点赞明细
        PostLike::create([
            'post_id'    => $post->id,
            'ip_address' => $ip,
            'liked_at'   => now(),
        ]);

        // 增加文章总点赞数
        $post->increment('likes');

        // 更新今日点赞汇总
        VisitSummary::firstOrCreate(
            ['summary_date' => now()->toDateString()],
            ['page_views' => 0, 'post_reads' => 0, 'likes_count' => 0, 'unique_visitors' => 0]
        )->increment('likes_count');

        return response()->json([
            'success' => true,
            'message' => '点赞成功',
            'likes'   => $post->fresh()->likes,
        ]);
    }
}
