@extends('layouts.admin')
@section('title')
    Dashboard
@endsection
@section('content')
    <section id="content" class="">
        <div class="row">
            <div class="col-xs-12 col-md-10">
                <div class="pl15 pr50">
                    @if(!auth()->user()->isAdmin())
                        <h4> {{ trans('admin.welcome') }} </h4>
                        <hr class="alt short">
                        <p class="text-muted"> Lorem ipsum dolor sit amet, is nisi ut aliquip ex ea commodo consectetur
                            adipi sicing elit, sed do eiusmod
                            tempor incididu ut labore et is nisi ut aliquip ex ea commodo dolore magna aliqua. Ut enim
                            ad
                            minim veniam, quis nostrud
                            exetation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                    @endif
                </div>
            </div>
        </div>
        @if(auth()->user()->isAdmin())
            <div class="row">
                <div class="col-xs-12 col-md-10">
                    <statistics source="{{ route('product-info') }}"></statistics>
                </div>
            </div>
            <hr class="my-5">
            <div class="row">
                <div class="col-xs-12 col-md-10">
                    <search-info source="{{ route('search-info') }}"></search-info>
                </div>
            </div>
        @endif
    </section>
@endsection
