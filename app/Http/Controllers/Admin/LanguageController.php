<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * 后台语言切换控制器
 * 接收语言参数并写入 Session，然后跳转回原页面
 */
class LanguageController extends Controller
{
    /**
     * 支持切换的语言列表
     */
    protected array $supportedLocales = ['zh_CN', 'en'];

    /**
     * 切换应用语言
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, string $locale)
    {
        // 仅允许切换到支持的语言
        if (in_array($locale, $this->supportedLocales, true)) {
            Session::put('app_locale', $locale);
        }

        // 返回用户之前所在的页面
        return redirect()->back();
    }
}
