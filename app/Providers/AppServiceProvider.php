<?php

namespace App\Providers;

use App\Models\Comment;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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

        // 注册后台自定义紧凑样式
        Filament::registerStyles([
            asset('css/filament-custom.css'),
        ]);

        // 向主布局共享当前登录用户的未读消息数量
        View::composer('layouts.app', function ($view) {
            $count = 0;

            if (Auth::check()) {
                $count = Comment::unread()
                    ->relatedToUser(Auth::user())
                    ->count();
            }

            $view->with('unreadMessageCount', $count);
        });
    }
}
