<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 注册应用服务
     */
    public function register()
    {
        //
    }

    /**
     * 启动应用服务
     * 注册 Filament 后台语言切换组件到用户菜单
     */
    public function boot()
    {
        // 在用户菜单开始位置注入语言切换组件
        Filament::registerRenderHook(
            'user-menu.start',
            fn (): string => view('filament.language-switcher')->render()
        );
    }
}
