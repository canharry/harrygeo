<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Visit;
use App\Services\GeoService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * 访问记录工厂
 * 用于生成仪表盘演示数据
 */
class VisitFactory extends Factory
{
    /**
     * 工厂对应模型
     */
    protected $model = Visit::class;

    /**
     * 定义模型的默认状态
     */
    public function definition()
    {
        // 随机生成一个公网风格的 IP，用于演示世界地图
        $ip = implode('.', [
            $this->faker->numberBetween(1, 223),
            $this->faker->numberBetween(0, 255),
            $this->faker->numberBetween(0, 255),
            $this->faker->numberBetween(1, 254),
        ]);

        $country = GeoService::getCountry($ip);
        $isPost = $this->faker->boolean(70); // 70% 概率为文章阅读
        $post = $isPost ? Post::inRandomOrder()->first() : null;

        return [
            'ip_address'   => $ip,
            'country_code' => $country['code'],
            'country_name' => $country['name'],
            'page_url'     => $post
                ? url('/posts/' . $post->slug)
                : url('/'),
            'post_id'      => $post?->id,
            'visited_at'   => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
