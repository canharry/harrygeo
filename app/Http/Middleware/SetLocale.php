<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/**
 * 设置应用语言中间件
 * 从 Session 中读取用户选择的语言并设置到 Laravel 应用
 */
class SetLocale
{
    /**
     * 支持切换的语言列表
     */
    protected array $supportedLocales = ['zh_CN', 'en'];

    /**
     * 处理请求
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 优先从 session 获取用户手动选择的语言
        $locale = Session::get('app_locale');

        // 如果 session 中没有，则使用浏览器首选语言
        if (! $locale) {
            $locale = $request->getPreferredLanguage($this->supportedLocales);
        }

        // 仅允许切换到支持的语言
        if (in_array($locale, $this->supportedLocales, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
