<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * 运行数据库填充
     * 创建默认用户、分类、标签和文章，供首页展示
     */
    public function run()
    {
        // 创建默认博主账号
        $user = User::factory()->create([
            'name'  => '阳光每一天',
            'email' => 'blogger@example.com',
        ]);

        // 创建 6 个分类
        $categories = Category::factory()->count(6)->create();

        // 创建 15 个标签
        $tags = Tag::factory()->count(15)->create();

        // 创建 24 篇文章，每篇文章随机绑定一个分类和 2-4 个标签
        Post::factory()
            ->count(24)
            ->sequence(fn ($sequence) => [
                'category_id' => $categories->random()->id,
                'user_id'     => $user->id,
            ])
            ->create()
            ->each(function (Post $post) use ($tags) {
                $post->tags()->attach(
                    $tags->random(rand(2, 4))->pluck('id')->toArray()
                );
            });
    }
}
