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
            'cover_image'   => $coverImage,
            'views'         => $this->faker->numberBetween(100, 9999),
            'likes'         => $this->faker->numberBetween(10, 999),
            'is_published'  => true,
            'published_at'  => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
