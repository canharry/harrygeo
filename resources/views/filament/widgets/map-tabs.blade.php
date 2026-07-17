@php
    // 将国家代码统一转换为小写，以兼容 jsVectorMap 的 world 数据
    $normalizedCountries = collect($countryData)
        ->mapWithKeys(fn ($count, $code) => [strtolower($code) => (int) $count])
        ->toArray();

    // 将中国省份代码统一保持大写，以兼容 jsVectorMap 的 cn_merc 数据
    $normalizedRegions = collect($regionData)
        ->mapWithKeys(fn ($count, $code) => [strtoupper($code) => (int) $count])
        ->toArray();
@endphp

<x-filament::widget>
    <x-filament::card>
        {{-- 使用 Alpine.js 管理标签页状态，整体用 wire:ignore 避免 Livewire 重绘破坏地图 --}}
        <div x-data="{ activeTab: 'world' }" wire:ignore>
            {{-- 标签页头部 --}}
            <div class="flex items-center justify-between mb-4">
                <div class="inline-flex rounded-lg border border-gray-300 bg-gray-100 p-1 dark:border-gray-600 dark:bg-gray-700">
                    <button
                        type="button"
                        @click="activeTab = 'world'; $nextTick(() => $dispatch('init-world-map'))"
                        :class="activeTab === 'world'
                            ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400'
                            : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'"
                        class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors"
                    >
                        {{ __('filament.widgets.world_map_title') }}
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'china'; $nextTick(() => $dispatch('init-china-map'))"
                        :class="activeTab === 'china'
                            ? 'bg-white text-primary-600 shadow-sm dark:bg-gray-800 dark:text-primary-400'
                            : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200'"
                        class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors"
                    >
                        {{ __('filament.widgets.china_map_title') }}
                    </button>
                </div>

                <div class="text-right">
                    <span class="block text-2xl font-bold text-primary-600" x-text="activeTab === 'world' ? '{{ number_format($worldTotal) }}' : '{{ number_format($chinaTotal) }}'"></span>
                    <span class="text-xs text-gray-500">{{ __('filament.widgets.total_visits') }}</span>
                </div>
            </div>

            {{-- 世界地图面板 --}}
            <div x-show="activeTab === 'world'" x-cloak>
                <div class="mb-2">
                    <h3 class="text-lg font-medium">{{ __('filament.widgets.world_map_title') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('filament.widgets.world_map_desc') }}</p>
                </div>
                <div
                    id="dashboard-world-map"
                    data-countries="{{ json_encode($normalizedCountries) }}"
                    data-legend="{{ __('filament.widgets.visits_legend') }}"
                    data-unit="{{ __('filament.widgets.visits_unit') }}"
                    style="width: 100%; height: 420px; min-height: 420px;"
                ></div>
            </div>

            {{-- 中国地图面板 --}}
            <div x-show="activeTab === 'china'" x-cloak>
                <div class="mb-2">
                    <h3 class="text-lg font-medium">{{ __('filament.widgets.china_map_title') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('filament.widgets.china_map_desc') }}</p>
                </div>
                <div
                    id="dashboard-china-map"
                    data-regions="{{ json_encode($normalizedRegions) }}"
                    data-legend="{{ __('filament.widgets.visits_legend') }}"
                    data-unit="{{ __('filament.widgets.visits_unit') }}"
                    style="width: 100%; height: 420px; min-height: 420px;"
                ></div>
            </div>
        </div>
    </x-filament::card>

    {{-- jsVectorMap 核心库通过共享局部视图只加载一次 --}}
    @include('filament.widgets._jsvectormap-assets')

    {{-- 世界地图与中国地图数据 --}}
    <script src="{{ asset('vendor/jsvectormap/world.js') }}"></script>
    <script src="{{ asset('vendor/jsvectormap/cn_merc.js') }}"></script>

    {{-- 地图初始化逻辑 --}}
    <script>
        (function () {
            function initDashboardWorldMap() {
                const container = document.getElementById('dashboard-world-map');
                if (!container || container.dataset.jsvectormapInitialized) {
                    return;
                }

                if (typeof jsVectorMap === 'undefined' || container.offsetWidth === 0) {
                    setTimeout(initDashboardWorldMap, 100);
                    return;
                }

                let countryData = {};
                try {
                    countryData = JSON.parse(container.dataset.countries || '{}');
                } catch (e) {
                    console.error('世界地图数据解析失败', e);
                    return;
                }

                container.dataset.jsvectormapInitialized = 'true';

                try {
                    new jsVectorMap({
                        selector: '#dashboard-world-map',
                        map: 'world',
                        backgroundColor: 'transparent',
                        regionStyle: {
                            initial: { fill: '#e5e7eb' },
                            hover: { fillOpacity: 1 },
                        },
                        series: {
                            regions: [{
                                attribute: 'fill',
                                legend: { title: container.dataset.legend || 'Visits' },
                                values: countryData,
                                scale: ['#dbeafe', '#2563eb', '#1e40af'],
                                normalizeFunction: 'polynomial',
                            }],
                        },
                        onRegionTooltipShow: function (event, tooltip, code) {
                            const count = countryData[code] || 0;
                            tooltip.text(
                                tooltip.text() + ' - ' + count + ' ' + (container.dataset.unit || 'visits')
                            );
                        },
                    });
                } catch (e) {
                    console.error('世界地图初始化失败', e);
                }
            }

            function initDashboardChinaMap() {
                const container = document.getElementById('dashboard-china-map');
                if (!container || container.dataset.jsvectormapInitialized) {
                    return;
                }

                if (typeof jsVectorMap === 'undefined' || container.offsetWidth === 0) {
                    setTimeout(initDashboardChinaMap, 100);
                    return;
                }

                let regionData = {};
                try {
                    regionData = JSON.parse(container.dataset.regions || '{}');
                } catch (e) {
                    console.error('中国地图数据解析失败', e);
                    return;
                }

                container.dataset.jsvectormapInitialized = 'true';

                try {
                    new jsVectorMap({
                        selector: '#dashboard-china-map',
                        map: 'cn_merc',
                        backgroundColor: 'transparent',
                        regionStyle: {
                            initial: { fill: '#e5e7eb' },
                            hover: { fillOpacity: 1 },
                        },
                        series: {
                            regions: [{
                                attribute: 'fill',
                                legend: { title: container.dataset.legend || 'Visits' },
                                values: regionData,
                                scale: ['#dbeafe', '#2563eb', '#1e40af'],
                                normalizeFunction: 'polynomial',
                            }],
                        },
                        onRegionTooltipShow: function (event, tooltip, code) {
                            const count = regionData[code] || 0;
                            tooltip.text(
                                tooltip.text() + ' - ' + count + ' ' + (container.dataset.unit || 'visits')
                            );
                        },
                    });
                } catch (e) {
                    console.error('中国地图初始化失败', e);
                }
            }

            // 页面加载完成后初始化世界地图（默认标签页）
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDashboardWorldMap);
            } else {
                initDashboardWorldMap();
            }

            // 标签切换时触发的初始化事件
            window.addEventListener('init-world-map', initDashboardWorldMap);
            window.addEventListener('init-china-map', initDashboardChinaMap);

            // 兼容 Filament/Livewire 的 DOM 更新
            if (typeof window.Livewire !== 'undefined') {
                window.Livewire.hook('message.processed', initDashboardWorldMap);
            }
        })();
    </script>
</x-filament::widget>
