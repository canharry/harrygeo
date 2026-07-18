<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * 站点设置模型
 * 用于存储可在后台动态配置的站点级文案、开关等。
 */
class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'label', 'group'];

    /**
     * 获取指定 key 的设置值，不存在时返回默认值
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('site_settings', now()->addHour(), function () {
            return static::all()->keyBy('key')->map->value;
        });

        return $settings->get($key, $default);
    }

    /**
     * 设置指定 key 的值（如未存在则创建）
     *
     * @param string $key
     * @param mixed $value
     * @param string $label
     * @param string $group
     * @return static
     */
    public static function setValue(string $key, mixed $value, string $label = '', string $group = 'general'): static
    {
        Cache::forget('site_settings');

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'label' => $label ?: $key, 'group' => $group]
        );
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('site_settings'));
        static::deleted(fn () => Cache::forget('site_settings'));
    }
}
