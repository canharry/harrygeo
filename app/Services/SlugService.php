<?php

namespace App\Services;

use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

/**
 * URL 别名生成服务
 *
 * 解决 Laravel Str::slug 对纯中文、日文、韩文等非拉丁字符会返回空字符串的问题。
 * 实现策略：
 * 1. 优先使用 Laravel 原生的 Str::slug（对英文/数字标题效果最好）。
 * 2. 若结果为空，则使用 overtrue/pinyin 将中文转为拼音 slug。
 * 3. 若拼音后仍为空（极端情况），则使用时间戳 + 随机数兜底，保证不为空。
 */
class SlugService
{
    /**
     * 生成 URL 友好的别名
     *
     * @param  string  $text      原始文本
     * @param  string|null  $fallback  当无法生成有意义 slug 时使用的兜底前缀
     * @return string
     */
    public static function make(string $text, ?string $fallback = null): string
    {
        $text = trim($text);

        if ($text === '') {
            return static::fallbackSlug($fallback);
        }

        // 1. 先尝试 Laravel 原生 slug（保留英文、数字及拉丁字符）
        $slug = Str::slug($text);

        if ($slug !== '') {
            return $slug;
        }

        // 2. 原生 slug 为空时，尝试将中文转为拼音
        $pinyin = app(Pinyin::class)->permalink($text);

        if ($pinyin !== '') {
            // permalink 已返回用 - 连接的字符串，再做一次清理确保规范
            return Str::slug($pinyin);
        }

        // 3. 兜底
        return static::fallbackSlug($fallback);
    }

    /**
     * 生成唯一别名
     *
     * @param  string  $text     原始文本
     * @param  string  $table    数据库表名
     * @param  string  $column   需要保证唯一的字段名
     * @param  int|null  $ignoreId  更新记录时需要忽略的主键 ID
     * @param  string|null  $fallback 兜底前缀
     * @return string
     */
    public static function unique(string $text, string $table, string $column = 'slug', ?int $ignoreId = null, ?string $fallback = null): string
    {
        $slug = static::make($text, $fallback);
        $originalSlug = $slug;
        $counter = 1;

        while (\DB::table($table)->where($column, $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * 兜底 slug
     */
    protected static function fallbackSlug(?string $prefix = null): string
    {
        return ($prefix ? $prefix . '-' : '') . time() . '-' . mt_rand(1000, 9999);
    }
}
