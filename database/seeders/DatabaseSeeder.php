<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Tag;
use App\Models\User;
use App\Models\Visit;
use App\Models\VisitSummary;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * 运行数据库填充
     * 创建默认用户、分类、标签、文章以及访问/点赞演示数据
     */
    public function run()
    {
        // 创建默认博主账号，同时标记为管理员，可登录 Filament 后台
        $user = User::factory()->create([
            'name'     => '阳光每一天',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
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

        // 生成近 30 天访问记录，供仪表盘统计与世界地图展示
        Visit::factory()->count(500)->create();

        // 生成近 30 天点赞记录
        PostLike::factory()->count(200)->create();

        // 根据已生成的访问与点赞数据，预聚合每日汇总表
        $this->buildVisitSummaries();
    }

    /**
     * 根据访问记录和点赞记录构建每日汇总数据
     */
    protected function buildVisitSummaries(): void
    {
        // 清空旧汇总（重新填充时使用）
        VisitSummary::query()->delete();

        // 按日期聚合浏览量与文章阅读量
        $visitStats = Visit::query()
            ->select(
                DB::raw('DATE(visited_at) as summary_date'),
                DB::raw('COUNT(*) as page_views'),
                DB::raw('SUM(CASE WHEN post_id IS NOT NULL THEN 1 ELSE 0 END) as post_reads'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_visitors')
            )
            ->groupBy(DB::raw('DATE(visited_at)'))
            ->get()
            ->keyBy('summary_date');

        // 按日期聚合点赞量
        $likeStats = PostLike::query()
            ->select(
                DB::raw('DATE(liked_at) as summary_date'),
                DB::raw('COUNT(*) as likes_count')
            )
            ->groupBy(DB::raw('DATE(liked_at)'))
            ->get()
            ->keyBy('summary_date');

        // 合并两个结果并写入汇总表
        $dates = $visitStats->keys()->merge($likeStats->keys())->unique()->sort();

        foreach ($dates as $date) {
            VisitSummary::create([
                'summary_date'    => Carbon::parse($date),
                'page_views'      => $visitStats[$date]?->page_views ?? 0,
                'post_reads'      => $visitStats[$date]?->post_reads ?? 0,
                'likes_count'     => $likeStats[$date]?->likes_count ?? 0,
                'unique_visitors' => $visitStats[$date]?->unique_visitors ?? 0,
            ]);
        }
    }
}
