<?php

namespace App\Http\Middleware;

use App\Models\AiVisit;
use App\Models\Post;
use App\Models\PostAiReference;
use App\Models\Visit;
use App\Models\VisitSummary;
use App\Services\GeoService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * 访问跟踪中间件
 * 在请求处理完成后异步记录访问日志，并更新每日访问汇总数据
 */
class TrackVisit
{
    /**
     * 需要跳过的常见爬虫 UA 关键字
     */
    protected array $botKeywords = [
        'bot', 'crawl', 'spider', 'slurp', 'bingpreview', 'facebookexternalhit',
        'googlebot', 'baiduspider', 'yandex', 'sogou', '360spider',
    ];

    /**
     * 已知 AI 平台爬虫 UA 关键字映射
     * 键为展示名称，值为匹配关键字（小写）
     */
    protected array $aiBots = [
        'Kimi'       => ['kimibot', 'kimi'],
        'DeepSeek'   => ['deepseekbot', 'deepseek'],
        '豆包'       => ['bytespider'],
        'ChatGPT'    => ['gptbot', 'chatgpt-user', 'openai'],
        'Gemini'     => ['google-extended'],
        'Claude'     => ['claudebot', 'anthropic-ai'],
        '文心一言'   => ['baiduspider'],
        '千问'       => ['qwen'],
        '智谱清言'   => ['chatglm'],
        '讯飞星火'   => ['spark'],
        '天工'       => ['tiangong'],
        'MiniMax'    => ['minimax'],
    ];

    /**
     * 处理请求
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * 在响应发送后记录访问数据
     * 使用 terminate 可避免影响页面响应速度
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            $postId = $this->resolvePostId($request);

            // 识别并记录 AI 爬虫访问（不计入普通访问统计）
            $aiName = $this->detectAiBot($request);
            if ($aiName && $postId) {
                $this->recordAiReference($request, $postId, $aiName);
            }

            if (! $this->shouldTrack($request)) {
                return;
            }

            $ip = $request->ip() ?? '127.0.0.1';
            $country = GeoService::getCountry($ip);
            $regionCode = $country['code'] === 'CN' ? GeoService::getChinaRegionCode($ip) : null;

            // 写入访问记录
            Visit::create([
                'ip_address'   => $ip,
                'country_code' => $country['code'],
                'country_name' => $country['name'],
                'region_code'  => $regionCode,
                'page_url'     => $request->fullUrl(),
                'post_id'      => $postId,
                'visited_at'   => now(),
            ]);

            // 更新当日汇总：浏览量 +1，文章阅读再 +1
            $this->updateSummary($postId);
        } catch (\Throwable $e) {
            // 访问统计不应影响主业务，出错时记录日志即可
            Log::warning('访问跟踪失败：' . $e->getMessage());
        }
    }

    /**
     * 判断当前请求是否需要记录
     */
    protected function shouldTrack(Request $request): bool
    {
        // 仅记录 GET 请求
        if (! $request->isMethod('GET')) {
            return false;
        }

        // 跳过后台管理、静态资源及非页面请求
        if ($request->is('admin/*', 'filament/*', 'livewire/*', 'storage/*')) {
            return false;
        }

        // 跳过常见爬虫
        $userAgent = strtolower($request->userAgent() ?? '');
        foreach ($this->botKeywords as $keyword) {
            if (str_contains($userAgent, $keyword)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 解析当前请求关联的文章 ID
     */
    protected function resolvePostId(Request $request): ?int
    {
        if ($request->route()?->getName() !== 'posts.show') {
            return null;
        }

        $slug = $request->route('slug');
        if (empty($slug)) {
            return null;
        }

        return Post::where('slug', $slug)->value('id');
    }

    /**
     * 更新当日访问汇总数据
     */
    protected function updateSummary(?int $postId): void
    {
        $today = now()->toDateString();

        $summary = VisitSummary::firstOrCreate(
            ['summary_date' => $today],
            ['page_views' => 0, 'post_reads' => 0, 'likes_count' => 0, 'unique_visitors' => 0]
        );

        $summary->increment('page_views');
        if ($postId !== null) {
            $summary->increment('post_reads');
        }
    }

    /**
     * 识别当前请求是否来自已知 AI 爬虫
     */
    protected function detectAiBot(Request $request): ?string
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        if ($userAgent === '') {
            return null;
        }

        foreach ($this->aiBots as $name => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($userAgent, $keyword)) {
                    return $name;
                }
            }
        }

        return null;
    }

    /**
     * 记录 AI 平台对文章的收录次数
     */
    protected function recordAiReference(Request $request, int $postId, string $aiName): void
    {
        PostAiReference::updateOrCreate(
            ['post_id' => $postId, 'name' => $aiName],
            ['sort_order' => 0]
        )->increment('count');

        // 同时记录 AI 访问明细，供后台列表查询
        AiVisit::create([
            'post_id'    => $postId,
            'ai_name'    => $aiName,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_url'   => $request->fullUrl(),
            'visited_at' => now(),
        ]);
    }
}
