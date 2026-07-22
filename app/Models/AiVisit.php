<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AI 访问记录模型
 * 记录各 AI 平台每次访问文章的明细
 */
class AiVisit extends Model
{
    use HasFactory;

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'post_id',
        'ai_name',
        'ip_address',
        'user_agent',
        'page_url',
        'visited_at',
    ];

    /**
     * 字段类型转换
     */
    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * 关联文章
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
