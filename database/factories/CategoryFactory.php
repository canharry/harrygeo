<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * 分类模型工厂
 * 用于生成博客分类的测试数据
 */
class CategoryFactory extends Factory
{
    /**
     * 定义模型的默认状态
     */
    public function definition()
    {
        // 预定义的动漫风格配色
        $colors = ['#ff7eb3', '#667eea', '#f093fb', '#4facfe', '#43e97b', '#fa709a', '#fee140'];

        // 预定义的分类图标
        $icons = ['bi-laptop', 'bi-code-square', 'bi-book', 'bi-camera', 'bi-music-note-beamed', 'bi-controller'];

        $name = $this->faker->randomElement([
            '技术随笔',
            '前端开发',
            '后端开发',
            '生活随拍',
            '动漫杂谈',
            '读书笔记',
            '旅行日记',
        ]);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 999),
            'description' => $this->faker->sentence(6),
            'icon'        => $this->faker->randomElement($icons),
            'color'       => $this->faker->randomElement($colors),
            'sort_order'  => $this->faker->numberBetween(0, 100),
            'is_show'     => true,
        ];
    }
}
