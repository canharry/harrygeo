<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 文章分类模型
 * 用于管理博客左侧边栏中的个人分类
 */
class Category extends Model
{
    use HasFactory;

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'name',        // 分类名称
        'slug',        // URL 友好标识
        'description', // 分类描述
        'icon',        // 分类图标类名
        'color',       // 分类颜色
        'sort_order',  // 排序权重
        'is_show',     // 是否显示
    ];

    /**
     * 一个分类下有多篇文章
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
