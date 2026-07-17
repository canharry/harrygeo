<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Tag;
use App\Models\VisitSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        // 根据 slug 查询已发布文章，并预加载分类、标签、作者、评论及评论的回复
        $post = Post::with([
            'category',
            'tags',
            'user',
            'comments' => function ($query) {
                $query->with(['user', 'nestedReplies'])
                      ->whereNull('parent_id')
                      ->orderBy('created_at');
            },
            'aiReferences',
        ])
            ->withCount('comments')
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

    /**
     * 保存文章评论
     *
     * @param string $slug 文章 URL 标识
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeComment($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $validated = request()->validate([
            'content'   => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ], [
            'content.required' => '评论内容不能为空',
            'content.max'      => '评论内容最多 1000 字',
        ]);

        // 如果指定了 parent_id，必须确保被回复的评论属于当前文章
        if (!empty($validated['parent_id'])) {
            $parentExists = Comment::where('id', $validated['parent_id'])
                ->where('post_id', $post->id)
                ->exists();

            if (! $parentExists) {
                return back()->withErrors(['parent_id' => '回复目标不存在']);
            }
        }

        Comment::create([
            'post_id'    => $post->id,
            'user_id'    => Auth::id(),
            'parent_id'  => $validated['parent_id'] ?? null,
            'content'    => $validated['content'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('posts.show', $post->slug)
            ->with('success', '评论发表成功');
    }

    /**
     * 删除文章评论
     *
     * 规则：
     * - 只能删除自己发表的评论
     * - 当该评论已有他人回复时不能删除
     * - 管理员可在后台删除任意评论
     *
     * @param string $slug 文章 URL 标识
     * @param \App\Models\Comment $comment 要删除的评论
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyComment($slug, Comment $comment)
    {
        $post = Post::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // 确保评论属于当前文章
        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        // 只能删除自己的评论
        if ($comment->user_id !== Auth::id()) {
            return back()->withErrors(['error' => '您只能删除自己的评论']);
        }

        // 若已有回复，则不允许删除
        if ($comment->replies()->exists()) {
            return back()->withErrors(['error' => '该评论已有回复，无法删除']);
        }

        // 清理评论中不再被引用的图片文件
        foreach ($comment->extractImages() as $path) {
            Comment::deleteImageIfUnused($path);
        }

        $comment->delete();

        return redirect()->route('posts.show', $post->slug)
            ->with('success', '评论已删除');
    }

    /**
     * 上传评论图片
     *
     * 通过 AJAX 上传图片并返回访问地址与存储路径
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadCommentImage(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ], [
            'image.required' => '请选择图片',
            'image.image'    => '请上传图片文件',
            'image.mimes'    => '仅支持 jpg、jpeg、png、gif、webp 格式',
            'image.max'      => '图片大小不能超过 2MB',
        ]);

        $path = $request->file('image')->store('comments/' . now()->format('Y/m'), 'public');

        return response()->json([
            'url'  => asset('storage/' . $path),
            'path' => $path,
        ]);
    }

    /**
     * 修改文章评论
     *
     * 规则：
     * - 只能修改自己发表的评论
     * - 当该评论已有他人回复时不能修改
     *
     * @param string $slug 文章 URL 标识
     * @param \App\Models\Comment $comment 要修改的评论
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateComment($slug, Comment $comment)
    {
        $post = Post::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // 确保评论属于当前文章
        if ($comment->post_id !== $post->id) {
            abort(404);
        }

        // 只能修改自己的评论
        if ($comment->user_id !== Auth::id()) {
            return back()->withErrors(['error' => '您只能修改自己的评论']);
        }

        // 若已有回复，则不允许修改
        if ($comment->replies()->exists()) {
            return back()->withErrors(['error' => '该评论已有回复，无法修改']);
        }

        $oldImages = $comment->extractImages();

        $validated = request()->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => '评论内容不能为空',
            'content.max'      => '评论内容最多 1000 字',
        ]);

        $newImages = Comment::extractImagePaths($validated['content']);

        // 删除旧内容中使用、但新内容中不再使用的图片文件
        foreach (array_diff($oldImages, $newImages) as $path) {
            Comment::deleteImageIfUnused($path, $comment->id);
        }

        $comment->update([
            'content' => $validated['content'],
        ]);

        return redirect()->route('posts.show', $post->slug)
            ->with('success', '评论已修改');
    }
}
