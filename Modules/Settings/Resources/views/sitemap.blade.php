@extends('layouts.admin')
@section('title')
    @lang('settings::settings.sitemap')
@endsection
@section('content')
    <p class="title_form">
        @lang('settings::settings.sitemap')
    </p>
    <p>
        <a href="{{ route('admin-settings-sitemap-generate') }}" class="btn btn-primary">@lang('admin.generate_sitemap')</a>
    </p>
@endsection