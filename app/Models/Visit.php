<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 访问记录模型
 * 用于记录每次页面访问，支撑后台仪表盘统计和世界地图展示
 */
class Visit extends Model
{
    use HasFactory;

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'ip_address',
        'country_code',
        'country_name',
        'region_code',
        'page_url',
        'post_id',
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
