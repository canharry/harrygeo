<?php

namespace Database\Seeders;

use App\Models\FriendshipLink;
use Illuminate\Database\Seeder;

/**
 * 友情链接默认数据填充
 * 创建首页侧边栏默认展示的友情链接
 */
class FriendshipLinkSeeder extends Seeder
{
    /**
     * 运行数据库填充
     */
    public function run(): void
    {
        $links = [
            ['name' => 'Laravel 中文网', 'url' => 'https://laravel-china.org', 'sort_order' => 0, 'is_show' => true],
            ['name' => '阮一峰博客',     'url' => 'https://ruanyifeng.com',    'sort_order' => 1, 'is_show' => true],
            ['name' => '掘金',           'url' => 'https://juejin.cn',         'sort_order' => 2, 'is_show' => true],
        ];

        foreach ($links as $link) {
            FriendshipLink::updateOrCreate(
                ['url' => $link['url']],
                $link
            );
        }
    }
}
