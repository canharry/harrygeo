<x-filament::page>
    {{-- 系统信息页面：展示 Filament 相关链接与版本信息 --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- Filament 官方信息卡片 --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                    <x-heroicon-o-code class="h-6 w-6"/>
                </div>
                <div>
                    <h3 class="text-lg font-medium">Filament</h3>
                    <p class="text-sm text-gray-500">后台管理面板框架</p>
                </div>
            </div>

            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <p>
                    <span class="font-medium">官方文档：</span>
                    <a href="https://filamentphp.com/docs/2.x" target="_blank" class="text-primary-600 hover:underline">
                        https://filamentphp.com/docs/2.x
                    </a>
                </p>
                <p>
                    <span class="font-medium">GitHub：</span>
                    <a href="https://github.com/filamentphp/filament" target="_blank" class="text-primary-600 hover:underline">
                        filamentphp/filament
                    </a>
                </p>
            </div>
        </x-filament::card>

        {{-- 项目信息卡片 --}}
        <x-filament::card>
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-success-50 text-success-600">
                    <x-heroicon-o-globe class="h-6 w-6"/>
                </div>
                <div>
                    <h3 class="text-lg font-medium">阳光每一天</h3>
                    <p class="text-sm text-gray-500">个人博客后台管理系统</p>
                </div>
            </div>

            <div class="mt-4 space-y-2 text-sm text-gray-600">
                <p><span class="font-medium">Laravel 版本：</span>{{ app()->version() }}</p>
                <p><span class="font-medium">PHP 版本：</span>{{ PHP_VERSION }}</p>
                <p><span class="font-medium">当前语言：</span>{{ app()->getLocale() === 'zh_CN' ? '简体中文' : 'English' }}</p>
            </div>
        </x-filament::card>
    </div>
</x-filament::page>
