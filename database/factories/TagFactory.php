<?php

namespace Database\Factories;

use App\Services\SlugService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * 标签模型工厂
 * 用于生成博客标签云的测试数据
 */
class TagFactory extends Factory
{
    /**
     * 定义模型的默认状态
     */
    public function definition()
    {
        // 标签配色池
        $colors = ['#ff7eb3', '#667eea', '#f6d365', '#a18cd1', '#fbc2eb', '#8fd3f4', '#84fab0'];

        $name = $this->faker->unique()->randomElement([
            'Laravel',
            'Vue',
            'React',
            'PHP',
            'JavaScript',
            'CSS',
            'MySQL',
            'Docker',
            'Linux',
            '设计',
            '摄影',
            '日常',
            '动漫',
            '旅行',
            '美食',
        ]);

        return [
            'name'  => $name,
            'slug'  => SlugService::make($name, 'tag'),
            'color' => $this->faker->randomElement($colors),
        ];
    }
}
