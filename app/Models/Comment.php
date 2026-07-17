<?php

namespace App\Models;

use App\Services\IpLocationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'post_id',     // 所属文章 ID
        'user_id',     // 评论用户 ID
        'parent_id',   // 父评论 ID（支持嵌套回复）
        'content',     // 评论内容
        'ip_address',  // 评论者 IP 地址
        'user_agent',  // 评论者 User-Agent
        'is_read',     // 是否已读
    ];

    /**
     * 需要转换为原生类型的字段
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * 评论图片占位符正则
     */
    private const IMAGE_PATTERN = '/\[img:([a-zA-Z0-9_\-\/]+\.(?:jpe?g|png|gif|webp))\]/i';

    /**
     * 模型启动事件
     * 用于删除评论时兜底清理关联图片文件
     */
    protected static function booted()
    {
        static::deleting(function (self $comment) {
            foreach ($comment->extractImages() as $path) {
                static::deleteImageIfUnused($path, $comment->id);
            }
        });
    }

    /**
     * 解析评论内容：将图片占位符渲染为安全 img 标签，文本段转义
     *
     * @return string
     */
    public function parseContent(): string
    {
        $content = $this->content ?? '';
        $segments = preg_split(self::IMAGE_PATTERN, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        $html = '';
        foreach ($segments as $index => $segment) {
            if ($index % 2 === 0) {
                $html .= nl2br(e($segment), true);
                continue;
            }

            if (! self::isValidImagePath($segment)) {
                $html .= e('[img:' . $segment . ']');
                continue;
            }

            $html .= '<img src="' . e(asset('storage/' . $segment)) . '" alt="评论图片" class="comment-inline-image" loading="lazy">';
        }

        return $html;
    }

    /**
     * 提取当前评论内容中的图片路径
     *
     * @return array
     */
    public function extractImages(): array
    {
        return self::extractImagePaths($this->content ?? '');
    }

    /**
     * 从任意内容中提取图片路径
     *
     * @param string $content
     * @return array
     */
    public static function extractImagePaths(string $content): array
    {
        preg_match_all(self::IMAGE_PATTERN, $content, $matches);
        $paths = $matches[1] ?? [];

        return array_values(array_unique(array_filter($paths, fn ($path) => self::isValidImagePath($path))));
    }

    /**
     * 校验图片路径是否安全
     *
     * @param string $path
     * @return bool
     */
    public static function isValidImagePath(string $path): bool
    {
        if (empty($path) || str_contains($path, '..')) {
            return false;
        }

        return (bool) preg_match('/^comments\/\d{4}\/\d{2}\/[a-zA-Z0-9]+\.(jpe?g|png|gif|webp)$/i', $path);
    }

    /**
     * 删除不再被任何评论引用的图片文件
     *
     * @param string $path
     * @param int|null $excludeId 排除当前评论自身（用于更新场景）
     * @return void
     */
    public static function deleteImageIfUnused(string $path, ?int $excludeId = null): void
    {
        $query = self::where('content', 'like', '%[img:' . $path . ']%');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if (! $query->exists()) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * 根据 User-Agent 判断设备类型
     *
     * @return string
     */
    public function getDeviceTypeAttribute(): string
    {
        $agent = strtolower($this->user_agent ?? '');

        $mobilePatterns = '/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini|mobile|phone|tablet/i';

        return preg_match($mobilePatterns, $agent) ? '手机' : '电脑';
    }

    /**
     * 根据 IP 解析城市归属地
     *
     * @return string
     */
    public function getCityAttribute(): string
    {
        return app(IpLocationService::class)->getCity($this->ip_address) ?? '未知';
    }

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

    /**
     * 递归加载所有层级子回复
     */
    public function nestedReplies()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->with(['user', 'nestedReplies'])
            ->orderBy('created_at');
    }

    /**
     * 只查询未读评论
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * 查询与指定用户相关的评论
     * - 评论了该用户的文章
     * - 回复了该用户的评论
     * 排除用户自己对自己的操作
     */
    public function scopeRelatedToUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->whereHas('post', fn ($post) => $post->where('user_id', $user->id))
              ->where('comments.user_id', '!=', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->whereHas('parent', fn ($parent) => $parent->where('user_id', $user->id))
              ->where('comments.user_id', '!=', $user->id);
        });
    }

    /**
     * 将当前评论标记为已读
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }
}
