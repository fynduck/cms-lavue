@extends('layouts.admin')
@section('title')
    @lang('banner::admin.banners')
@endsection
@section('content')

    @permission(['banners', 'create'])
    <a href="{{ route('banners.create') }}" class="btn btn-primary mb-3">@lang('banner::admin.add_banner')</a>
    @endpermission
    <banner-list source="{{ route('banners-list') }}" :trans="{{ json_encode($trans) }}"></banner-list>
@endsection
