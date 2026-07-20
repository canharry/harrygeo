<?php

namespace App\Models;

use App\Services\ContentRenderer;
use App\Services\SlugService;
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
        'video',         // 视频文件路径或外部视频链接
        'views',         // 浏览量
        'likes',         // 点赞数
        'is_published',  // 是否发布
        'is_original',   // 是否为原创文章
        'original_url',  // 转载文章来源链接
        'published_at',  // 发布时间
    ];

    /**
     * 需要转换为日期的字段
     */
    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'is_original'  => 'boolean',
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

    /**
     * 文章被多个 AI 平台引用/收录
     */
    public function aiReferences()
    {
        return $this->hasMany(PostAiReference::class)->orderBy('sort_order');
    }

    /**
     * 获取可公开访问的视频地址
     * 本地文件自动转换为 storage 链接，外部链接原样返回
     */
    public function videoUrl(): ?string
    {
        if (empty($this->video)) {
            return null;
        }

        if (filter_var($this->video, FILTER_VALIDATE_URL)) {
            return $this->video;
        }

        return asset('storage/' . $this->video);
    }

    /**
     * 判断视频来源类型：youtube、bilibili、html5、none
     */
    public function videoType(): string
    {
        if (empty($this->video)) {
            return 'none';
        }

        $value = $this->video;

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $value, $matches)) {
            return 'youtube';
        }

        if (preg_match('/(?:bilibili\.com\/video\/|b23\.tv\/)(BV[a-zA-Z0-9]+)/', $value, $matches)) {
            return 'bilibili';
        }

        return 'html5';
    }

    /**
     * 获取 YouTube 视频 ID
     */
    public function youtubeVideoId(): ?string
    {
        if (! preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $this->video, $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * 获取 Bilibili 视频 BV 号
     */
    public function bilibiliVideoId(): ?string
    {
        if (! preg_match('/(?:bilibili\.com\/video\/|b23\.tv\/)(BV[a-zA-Z0-9]+)/', $this->video, $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * 根据标题生成唯一的 URL 别名
     * 对中文标题会自动转换为拼音 slug
     */
    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = SlugService::make($title, 'post');

        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * 获取渲染后的正文内容：解析 Markdown 表格并转换图片路径。
     */
    public function renderedContent(): string
    {
        return ContentRenderer::render($this->content ?? '');
    }
}
