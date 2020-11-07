<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-name="{{ config('app.name') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="author" content="Stanislav">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" hreflang="{{ config('app.locale_prefix') }}" href="{{ Request::url() }}">
    <link href="{{ asset('fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset(mix('admin/css/app.css')) }}" rel="stylesheet">
    <link href="{{ asset(mix('admin/css/admin.css')) }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('tinymce/plugins/codemirror/codemirror-5.23.0/lib/codemirror.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/png"/>
</head>
<body>
<!-- Start: Main -->
<div id="app">
    <admin-app></admin-app>
</div>
{{--<script src="{{ mix('js/manifest.js') }}"></script>--}}
{{--<script src="{{ mix('js/vendor.js') }}"></script>--}}
<script src="{{ mix('admin/js/app.js') }}"></script>

<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('tinymce/tinymce_editor.js') }}"></script>
<!-- END: PAGE SCRIPTS -->
</body>
</html>
