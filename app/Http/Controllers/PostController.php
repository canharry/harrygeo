<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

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
        $post = Post::with(['category', 'tags', 'user', 'comments.user'])
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

        return view('posts.show', compact('post', 'prevPost', 'nextPost', 'relatedPosts'));
    }
}
