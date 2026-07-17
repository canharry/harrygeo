<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

/**
 * 消息中心控制器
 * 展示与当前用户相关的评论消息
 */
class MessageController extends Controller
{
    /**
     * 消息列表页
     *
     * 进入页面时，将所有未读相关评论标记为已读，再分页展示全部相关评论。
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // 先将未读相关评论批量标记为已读
        $unreadIds = Comment::unread()
            ->relatedToUser($user)
            ->pluck('id');

        if ($unreadIds->isNotEmpty()) {
            Comment::whereIn('id', $unreadIds)->update(['is_read' => true]);
        }

        // 查询所有相关评论（含已读），按时间倒序分页
        $messages = Comment::with(['post', 'user', 'parent.user'])
            ->relatedToUser($user)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('messages.index', compact('messages'));
    }
}
