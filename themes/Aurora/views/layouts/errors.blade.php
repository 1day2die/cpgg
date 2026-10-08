<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php($website_settings = app(App\Settings\WebsiteSettings::class))

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="{{ $website_settings->seo_title }}" property="og:title">
    <meta content="{{ $website_settings->seo_description }}" property="og:description">
    <meta
        content='{{ \Illuminate\Support\Facades\Storage::disk('public')->exists('logo.png') ? asset('storage/logo.png') : asset('images/ctrlpanel_logo.png') }}'
        property="og:image">
    <title>{{ $errorCode }} · {{ config('app.name', 'CtrlPanel.gg') }}</title>
    <link rel="icon"
        href="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists('favicon.ico') ? asset('storage/favicon.ico') : asset('favicon.ico') }}"
        type="image/x-icon">

    <link rel="preload" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    </noscript>

    @include('aurora.head')
</head>

<body class="tw:flex tw:min-h-screen tw:items-center tw:justify-center tw:p-4 tw:sm:p-6">
    <main class="tw:card tw:w-full tw:max-w-lg tw:border tw:border-base-300 tw:bg-base-100 tw:shadow-xl">
        <div class="tw:card-body tw:items-center tw:gap-3 tw:p-8 tw:text-center tw:sm:p-10">
            <span class="tw:mb-1 tw:inline-flex tw:size-14 tw:items-center tw:justify-center tw:rounded-full tw:bg-error/15 tw:text-2xl tw:text-(--aurora-error-fg)"
                aria-hidden="true">
                <i class="fas fa-exclamation-triangle"></i>
            </span>

            <span class="tw:badge tw:badge-soft tw:badge-error tw:font-semibold tw:text-(--aurora-error-fg)">
                {{ __('ERROR') }} {{ $errorCode }}
            </span>

            <h1 class="tw:text-2xl tw:font-semibold tw:tracking-tight">{{ $title }}</h1>

            <p class="tw:max-w-sm tw:text-(--aurora-text-muted)">{{ $message }}</p>

            @if (($exception ?? false) && auth()->user()?->can('errors.view'))
                <div role="alert" class="tw:alert tw:alert-soft tw:w-full tw:text-left tw:text-sm">
                    <i class="fas fa-bug tw:text-(--aurora-text-muted)" aria-hidden="true"></i>
                    <code class="tw:break-all">{{ $exception->getMessage() }}</code>
                </div>
            @endif

            @if ($homeLink ?? false)
                <div class="tw:card-actions tw:mt-3">
                    <a href="{{ route('home') }}" class="tw:btn tw:btn-primary">
                        <i class="fas fa-home" aria-hidden="true"></i>
                        {{ __('Go home') }}
                    </a>
                </div>
            @endif
        </div>
    </main>
</body>

</html>
