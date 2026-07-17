<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * 文章模型工厂
 * 用于生成博客文章卡片的测试数据
 */
class PostFactory extends Factory
{
    /**
     * 定义模型的默认状态
     */
    public function definition()
    {
        // 封面图暂不设置真实图片地址
        // 当前图片生成接口会返回带黑边的占位图，因此先留空
        // 后续接入稳定图片来源后，可在此处恢复图片 URL
        $coverImage = null;

        $title = $this->faker->unique()->sentence(3);

        return [
            'title'         => $title,
            'slug'          => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'summary'       => $this->faker->paragraph(2),
            'content'       => $this->faker->paragraphs(6, true),
            'cover_image'   => $coverImage,
            'views'         => $this->faker->numberBetween(100, 9999),
            'likes'         => $this->faker->numberBetween(10, 999),
            'is_published'  => true,
            'published_at'  => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
