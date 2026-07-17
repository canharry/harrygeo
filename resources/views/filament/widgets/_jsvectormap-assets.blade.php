{{-- jsVectorMap 核心库与样式，通过 @once 确保仪表盘上只加载一次 --}}
@once
    <link rel="stylesheet" href="{{ asset('vendor/jsvectormap/jsvectormap.min.css') }}" />
    <script src="{{ asset('vendor/jsvectormap/jsvectormap.min.js') }}"></script>
@endonce
