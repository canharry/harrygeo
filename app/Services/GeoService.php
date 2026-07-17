<?php

namespace App\Services;

/**
 * IP 地理位置服务
 * 目前使用简单规则进行国家/地区推断，适合个人博客演示场景。
 * 若需要高精度定位，可替换为 MaxMind GeoIP2 或 ip-api.com 等第三方服务。
 */
class GeoService
{
    /**
     * 常见国家/地区代码与名称对照表
     */
    protected static array $countryMap = [
        'CN' => 'China',
        'US' => 'United States',
        'JP' => 'Japan',
        'KR' => 'South Korea',
        'GB' => 'United Kingdom',
        'DE' => 'Germany',
        'FR' => 'France',
        'RU' => 'Russia',
        'BR' => 'Brazil',
        'IN' => 'India',
        'AU' => 'Australia',
        'CA' => 'Canada',
        'SG' => 'Singapore',
        'HK' => 'Hong Kong',
        'TW' => 'Taiwan',
    ];

    /**
     * 根据 IP 地址获取国家/地区信息
     *
     * @param string|null $ip
     * @return array{code: string, name: string}
     */
    public static function getCountry(?string $ip): array
    {
        // 空 IP 或本地回环默认中国大陆
        if (empty($ip) || in_array($ip, ['127.0.0.1', '::1'], true)) {
            return ['code' => 'CN', 'name' => 'China'];
        }

        // 私有网段默认中国大陆（常见于本地开发或内网访问）
        if (self::isPrivateIp($ip)) {
            return ['code' => 'CN', 'name' => 'China'];
        }

        // 对公网 IP 做简单哈希分布，使演示数据在世界地图上呈现多样性
        // 注意：此方式不保证真实地理位置，仅用于展示效果
        $codes = array_keys(self::$countryMap);
        $index = abs(crc32($ip)) % count($codes);
        $code = $codes[$index];

        return [
            'code' => $code,
            'name' => self::$countryMap[$code],
        ];
    }

    /**
     * 判断是否为私有 IP 地址
     */
    protected static function isPrivateIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    /**
     * 中国省份代码与名称对照表（对应 jsVectorMap 的 cn_merc 数据）
     */
    protected static array $chinaRegionMap = [
        'CN-11' => '北京',
        'CN-12' => '天津',
        'CN-13' => '河北',
        'CN-14' => '山西',
        'CN-15' => '内蒙古',
        'CN-21' => '辽宁',
        'CN-22' => '吉林',
        'CN-23' => '黑龙江',
        'CN-31' => '上海',
        'CN-32' => '江苏',
        'CN-33' => '浙江',
        'CN-34' => '安徽',
        'CN-35' => '福建',
        'CN-36' => '江西',
        'CN-37' => '山东',
        'CN-41' => '河南',
        'CN-42' => '湖北',
        'CN-43' => '湖南',
        'CN-44' => '广东',
        'CN-45' => '广西',
        'CN-46' => '海南',
        'CN-50' => '重庆',
        'CN-51' => '四川',
        'CN-52' => '贵州',
        'CN-53' => '云南',
        'CN-54' => '西藏',
        'CN-61' => '陕西',
        'CN-62' => '甘肃',
        'CN-63' => '青海',
        'CN-64' => '宁夏',
        'CN-65' => '新疆',
        'CN-71' => '台湾',
    ];

    /**
     * 根据国家代码获取国家名称
     */
    public static function getCountryName(string $code): string
    {
        return self::$countryMap[$code] ?? 'Unknown';
    }

    /**
     * 根据 IP 地址获取中国省份代码
     *
     * 当前实现基于 IP 哈希做确定性分布，仅用于演示效果。
     * 如需真实地理位置，可替换为 MaxMind GeoIP2 等第三方服务。
     *
     * @param string|null $ip
     * @return string|null
     */
    public static function getChinaRegionCode(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }

        $codes = array_keys(self::$chinaRegionMap);
        $index = abs(crc32($ip)) % count($codes);

        return $codes[$index];
    }

    /**
     * 根据中国省份代码获取名称
     */
    public static function getChinaRegionName(string $code): string
    {
        return self::$chinaRegionMap[$code] ?? 'Unknown';
    }
}
