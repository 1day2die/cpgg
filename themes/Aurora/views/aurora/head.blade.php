{{-- Aurora: resolves the color mode before first paint, then loads fonts and the theme stylesheet. --}}
@php
    $auroraAsset = static function (string $path): string {
        $file = public_path('themes/Aurora/' . $path);

        return asset('themes/Aurora/' . $path) . '?v=' . (is_file($file) ? filemtime($file) : '1');
    };
@endphp
<meta name="color-scheme" content="dark light">
<script data-aurora-default="{{ config('theme.aurora.default_mode', 'dark') }}">
    (function () {
        var root = document.documentElement;
        var mode = document.currentScript.getAttribute('data-aurora-default');
        try {
            mode = localStorage.getItem('aurora-theme') || mode;
        } catch (e) {}
        if (['light', 'dark', 'system'].indexOf(mode) === -1) {
            mode = 'dark';
        }
        var dark = mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        root.setAttribute('data-theme-mode', mode);
        root.setAttribute('data-theme', dark ? 'aurora-dark' : 'aurora-light');
    })();
</script>
<link rel="preload" href="{{ asset('themes/Aurora/fonts/inter-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="{{ $auroraAsset('app.css') }}">
<script src="{{ $auroraAsset('aurora.js') }}" defer></script>
