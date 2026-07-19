<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 友情链接模型
 * 用于在后台管理首页侧边栏的友情链接列表
 */
class FriendshipLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'sort_order',
        'is_show',
    ];

    protected $casts = [
        'is_show'    => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * 只查询展示中的友情链接，并按排序值升序
     */
    public function scopeShown($query)
    {
        return $query->where('is_show', true)->orderBy('sort_order');
    }
}
