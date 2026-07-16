{{-- 后台语言切换组件：显示在用户菜单顶部 --}}
@php
    $currentLocale = app()->getLocale();
    $locales = [
        'zh_CN' => '简体中文',
        'en' => 'English',
    ];
@endphp

<form method="POST" action="{{ route('admin.language.switch', ['locale' => $currentLocale === 'zh_CN' ? 'en' : 'zh_CN']) }}" class="block w-full">
    @csrf
    <button type="submit"
            class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
        <x-heroicon-o-translate class="h-5 w-5 text-gray-500"/>
        <span>{{ $locales[$currentLocale] ?? '简体中文' }}</span>
        <span class="ml-auto text-xs text-gray-400">{{ $currentLocale === 'zh_CN' ? 'Switch to English' : '切换到中文' }}</span>
    </button>
</form>
