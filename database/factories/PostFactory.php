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
        // 封面图主题词，用于调用图片生成接口
        $coverThemes = [
            'anime%20style%20programming%20workspace%20with%20glowing%20screens',
            'anime%20style%20cat%20sleeping%20on%20laptop%20keyboard%20cute',
            'anime%20style%20sunset%20mountain%20landscape%20with%20clouds',
            'anime%20style%20cozy%20coffee%20shop%20with%20warm%20lights',
            'anime%20style%20starry%20night%20sky%20with%20milky%20way',
            'anime%20style%20cherry%20blossom%20street%20in%20spring',
        ];

        $title = $this->faker->randomElement([
            '基于 Laravel 构建个人博客的完整实践',
            '如何写出优雅的 CSS 动画效果',
            'Vue3 Composition API 入门指南',
            'Docker 部署 Laravel 项目踩坑记录',
            '我的 2025 年度书单与阅读感悟',
            '用 PHP 实现一个简单的队列系统',
            '前端性能优化：从图片懒加载开始',
            '动漫推荐：这个季度的治愈系番剧',
            '摄影笔记：如何拍出好看的日落',
            'MySQL 索引优化实战总结',
        ]);

        return [
            'title'         => $title,
            'slug'          => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'summary'       => $this->faker->paragraph(2),
            'content'       => $this->faker->paragraphs(6, true),
            'cover_image'   => 'https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt=' . $this->faker->randomElement($coverThemes) . '%2C%20pastel%20colors%2C%20high%20quality%20illustration&image_size=landscape_16_9',
            'views'         => $this->faker->numberBetween(100, 9999),
            'likes'         => $this->faker->numberBetween(10, 999),
            'is_published'  => true,
            'published_at'  => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
