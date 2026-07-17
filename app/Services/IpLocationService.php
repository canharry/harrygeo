<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * IP 归属地解析服务
 * 通过免费 API 将 IP 地址解析为城市名称
 */
class IpLocationService
{
    /**
     * 根据 IP 获取城市名称
     *
     * @param string|null $ip
     * @return string|null
     */
    public function getCity(?string $ip): ?string
    {
        if (empty($ip)) {
            return null;
        }

        // 本地或私有 IP 直接返回本地网络
        if ($this->isPrivateIp($ip)) {
            return '本地网络';
        }

        // 缓存 24 小时，避免重复请求
        return cache()->remember("ip_location:{$ip}", now()->addDay(), function () use ($ip) {
            return $this->fetchCity($ip);
        });
    }

    /**
     * 调用 ip-api.com 获取城市
     *
     * @param string $ip
     * @return string|null
     */
    protected function fetchCity(string $ip): ?string
    {
        try {
            $response = Http::timeout(3)
                ->get("http://ip-api.com/json/{$ip}", [
                    'lang'   => 'zh-CN',
                    'fields' => 'status,message,city',
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (($data['status'] ?? '') === 'success' && !empty($data['city'])) {
                    return $data['city'];
                }
            }
        } catch (\Throwable $e) {
            // 网络异常时静默返回 null
        }

        return null;
    }

    /**
     * 判断是否为私有/本地 IP
     *
     * @param string $ip
     * @return bool
     */
    protected function isPrivateIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
