<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

/**
 * 分类控制器
 * 负责分类归档页
 */
class CategoryController extends Controller
{
    /**
     * 全部分类列表页
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::withCount('posts')
            ->where('is_show', true)
            ->orderBy('sort_order')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * 分类归档页
     *
     * @param string $slug 分类 URL 标识
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // 根据 slug 查询分类，并预加载已发布文章
        $category = Category::where('slug', $slug)
            ->where('is_show', true)
            ->firstOrFail();

        // 获取该分类下已发布的文章，按发布时间倒序分页
        $posts = $category->posts()
            ->with(['tags', 'user'])
            ->withCount('comments')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        // 获取所有展示中的分类，用于侧边栏
        $categories = Category::withCount('posts')
            ->where('is_show', true)
            ->orderBy('sort_order')
            ->get();

        return view('categories.show', compact('category', 'posts', 'categories'));
    }
}
