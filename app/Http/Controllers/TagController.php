<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

/**
 * 标签控制器
 * 负责标签归档页
 */
class TagController extends Controller
{
    /**
     * 全部标签列表页
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tags = Tag::orderByDesc('id')->get();

        return view('tags.index', compact('tags'));
    }

    /**
     * 标签归档页
     *
     * @param string $slug 标签 URL 标识
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // 根据 slug 查询标签
        $tag = Tag::where('slug', $slug)->firstOrFail();

        // 获取该标签下已发布的文章，按发布时间倒序分页
        $posts = $tag->posts()
            ->with(['category', 'tags', 'user'])
            ->withCount('comments')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        // 获取标签云数据，用于侧边栏
        $tags = Tag::orderByDesc('id')
            ->take(30)
            ->get();

        return view('tags.show', compact('tag', 'posts', 'tags'));
    }
}
