<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * 文章点赞记录工厂
 * 用于生成仪表盘演示数据
 */
class PostLikeFactory extends Factory
{
    /**
     * 工厂对应模型
     */
    protected $model = PostLike::class;

    /**
     * 定义模型的默认状态
     */
    public function definition()
    {
        return [
            'post_id'  => Post::inRandomOrder()->first()?->id ?? Post::factory(),
            'ip_address' => implode('.', [
                $this->faker->numberBetween(1, 223),
                $this->faker->numberBetween(0, 255),
                $this->faker->numberBetween(0, 255),
                $this->faker->numberBetween(1, 254),
            ]),
            'liked_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
