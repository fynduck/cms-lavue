<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="author" content="Stanislav">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="//use.fontawesome.com/releases/v5.12.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/app.css') }}">
    <link href="{{ asset(mix('admin/css/admin.css')) }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
</head>
<body>
<section id="app">
    <admin-app></admin-app>
</section>

{{--<script src="{{ asset('js/manifest.js') }}"></script>--}}
{{--<script src="{{ asset('js/vendor.js') }}"></script>--}}
<script src="{{ mix('admin/js/app.js') }}"></script>
</body>
</html>
