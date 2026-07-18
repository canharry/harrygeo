<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

/**
 * 用户归档控制器
 * 展示指定用户（博主）使用过的分类与标签
 */
class UserArchiveController extends Controller
{
    /**
     * 指定用户文章使用过的分类列表
     *
     * @param \App\Models\User $user
     * @return \Illuminate\View\View
     */
    public function categories(User $user)
    {
        $categories = Category::withCount(['posts' => function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_published', true);
            }])
            ->where('is_show', true)
            ->whereHas('posts', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_published', true);
            })
            ->orderBy('sort_order')
            ->get();

        return view('users.categories', compact('user', 'categories'));
    }

    /**
     * 指定用户标签下的文章列表
     *
     * 展示该用户（博主）使用过的所有标签，以及这些标签下的文章。
     *
     * @param \App\Models\User $user
     * @return \Illuminate\View\View
     */
    public function tags(User $user)
    {
        $tags = Tag::whereHas('posts', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_published', true);
            })
            ->orderByDesc('id')
            ->get();

        $posts = Post::with(['category', 'tags', 'user'])
            ->withCount('comments')
            ->where('user_id', $user->id)
            ->where('is_published', true)
            ->whereHas('tags')
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('users.tags', compact('user', 'tags', 'posts'));
    }
}
