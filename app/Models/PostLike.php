<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 文章点赞记录模型
 * 用于记录每次点赞行为，支撑后台仪表盘每日点赞量统计
 */
class PostLike extends Model
{
    use HasFactory;

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'post_id',
        'ip_address',
        'liked_at',
    ];

    /**
     * 字段类型转换
     */
    protected $casts = [
        'liked_at' => 'datetime',
    ];

    /**
     * 关联文章
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
