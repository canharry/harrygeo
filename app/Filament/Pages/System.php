<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * 系统信息页面
 * 用于展示 Filament 相关链接和系统版本信息
 */
class System extends Page
{
    // 页面视图路径
    protected static string $view = 'filament.pages.system';

    // 页面标题
    protected static ?string $title = '系统信息';

    // 导航标签
    protected static ?string $navigationLabel = '系统信息';

    // 导航图标
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    // 导航分组：放入“系统”分组
    protected static ?string $navigationGroup = '系统';

    // URL 别名
    protected static ?string $slug = 'system';

    // 排序
    protected static ?int $navigationSort = 1;

    /**
     * 仅管理员可见
     */
    public static function canView(): bool
    {
        return auth()->user()->is_admin ?? false;
    }
}
