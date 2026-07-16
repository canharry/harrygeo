{{--
    自定义分页视图
    将 Laravel 默认分页转换为中文、适配本站动漫风格
--}}
@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="分页导航">
        <div class="pagination-summary">
            共 {{ $paginator->total() }} 条，第 {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }} 页
        </div>

        <ul class="pagination-list">
            {{-- 上一页 --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled" aria-disabled="true">
                    <span class="pagination-link">
                        <i class="bi bi-chevron-left"></i> 上一页
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left"></i> 上一页
                    </a>
                </li>
            @endif

            {{-- 页码 --}}
            @foreach ($elements as $element)
                {{-- 省略号 --}}
                @if (is_string($element))
                    <li class="pagination-item disabled" aria-disabled="true">
                        <span class="pagination-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- 页码数组 --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item active" aria-current="page">
                                <span class="pagination-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- 下一页 --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        下一页 <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pagination-item disabled" aria-disabled="true">
                    <span class="pagination-link">
                        下一页 <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
