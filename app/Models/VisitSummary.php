<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 访问汇总模型
 * 按天预聚合访问数据，用于仪表盘快速展示每日指标
 */
class VisitSummary extends Model
{
    use HasFactory;

    /**
     * 允许批量赋值的字段
     */
    protected $fillable = [
        'summary_date',
        'page_views',
        'post_reads',
        'likes_count',
        'unique_visitors',
    ];

    /**
     * 字段类型转换
     */
    protected $casts = [
        'summary_date' => 'date',
    ];
}
