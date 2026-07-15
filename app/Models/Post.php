<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 博客文章模型
 * 存储首页展示的文章卡片数据
 */
class Post extends Model
{
    use HasFactory;

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'category_id',   // 所属分类 ID
        'user_id',       // 作者 ID
        'title',         // 文章标题
        'slug',          // URL 友好标识
        'summary',       // 文章摘要
        'content',       // 文章内容
        'cover_image',   // 封面图地址
        'views',         // 浏览量
        'likes',         // 点赞数
        'is_published',  // 是否发布
        'published_at',  // 发布时间
    ];

    /**
     * 需要转换为日期的字段
     */
    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    /**
     * 文章属于一个分类
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 文章属于一个作者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 文章拥有多个标签
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * 文章拥有多条评论
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
