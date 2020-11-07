@extends('layouts.layout')
@section('content')
    <articles type="{{ $page->method }}"
              title="{{ $page->title }}"
              source="{{ route('get-articles') }}"></articles>
    <div class="container">
        @if(strip_tags($page->description))
            <div class="my-4">
                {!! $page->description !!}
            </div>
        @endif
        @if($page->socials == 1)
            @include('components.socials')
        @endif
    </div>
@endsection
@push('scripts')
    @if($page->socials == 1)
        @include('components.share-scripts')
    @endif
@endpush
