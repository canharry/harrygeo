<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 文章标签模型
 * 用于管理博客标签云
 */
class Tag extends Model
{
    use HasFactory;

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'name',  // 标签名称
        'slug',  // URL 友好标识
        'color', // 标签颜色
    ];

    /**
     * 一个标签可以对应多篇文章
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
