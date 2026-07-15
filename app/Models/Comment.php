<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 评论模型
 * 存储文章下方的用户评论
 */
class Comment extends Model
{
    use HasFactory;

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'post_id',   // 所属文章 ID
        'user_id',   // 评论用户 ID
        'parent_id', // 父评论 ID（支持嵌套回复）
        'content',   // 评论内容
    ];

    /**
     * 评论属于一篇文章
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * 评论属于一个用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 评论的父级评论
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * 评论的子回复
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
