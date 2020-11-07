<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, user-scalable=no">
    <title>{{ $meta_title ?? ''}}</title>
    <meta name="description" content="{{ $meta_description ?? '' }}">
    <meta name="keywords" content="{{ $meta_keywords ?? '' }}">
    <meta name="author" content="Stanislav">
    <meta property="og:site_name" content="{{ $settings['name_site'] ?? '' }}"/>
    <meta property="og:title" content="{{ $meta_title ?? '' }}"/>
    <meta property="og:url" content="{{ Request::url() }}"/>
    <meta property="og:description" content="{{ $meta_description ?? '' }}"/>
    @if(isset($seo_images))
        @if(is_array($seo_images))
            @foreach($seo_images as $image)
                <meta property="og:image" content="{{ asset($image) }}"/>
                <meta property="og:image:width" content="500"/>
                <meta property="og:image:height" content="350"/>
            @endforeach
        @else
            <meta property="og:image" content="{{ asset($seo_images) }}"/>
            <meta property="og:image:width" content="500"/>
            <meta property="og:image:height" content="350"/>
        @endif
    @endif
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}"/>
    <link rel="canonical" hreflang="{{ config('app.locale_prefix') }}" href="{{ Request::url() }}">
    <meta property="og:locale" content="{{ config('app.faker_locale') }}"/>
    <meta property="og:locale" content="{{config('app.locale')}}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">
    @stack('css')
    @if(File::exists(public_path('css/admin-css.css')))
        <link href="{{ asset('css/admin-css.css') }}" rel="preload" as="style">
    @endif
    @if(isset($settings['analytics_top']))
        {!! $settings['analytics_top'] !!}
    @endif
</head>
<body>
<div id="app">
    <header>
        @include('header')
    </header>
    <main>
        @include('errors.success')
        @include('errors.error')
        @include('components.breadcrumbs')
        @yield('content')
    </main>
{{--    <footer>--}}
{{--        @include('footer')--}}
{{--    </footer>--}}
    <scroll-top word="<i class='icofont-arrow-up'></i>" :right="true"></scroll-top>
</div>
<script src="{{ asset(mix('js/app.js')) }}"></script>
@stack('scripts')
@if(isset($settings['analytics']))
    {!! $settings['analytics'] !!}
@endif
</body>
</html>