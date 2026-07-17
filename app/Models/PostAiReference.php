<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 文章 AI 引用记录
 * 记录各 AI 平台对文章的收录次数
 */
class PostAiReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'name',
        'count',
        'sort_order',
    ];

    protected $casts = [
        'count' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * 所属文章
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
