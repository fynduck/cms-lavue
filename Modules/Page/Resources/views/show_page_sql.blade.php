@extends('layouts.layout')

@section('content')
    <section class="container">
        <h1 class="title_form">{{ $page->title }}</h1>
        {!! $page->description !!}
        <relate-products id="custom-products" class="mt-2"
                         source="{{ route('get-custom-products'. ['page_id' => $page->page_id]) }}"
                         page="other"
                         cart="{{ route('pages', isset($urlsPages['cart']) ? $urlsPages['cart'] : '#') }}"></relate-products>
        @if($page->socials == 1)
            <div class="my-2">
                @include('components.socials')
            </div>
        @endif
    </section>
@endsection
@push('scripts')
    @if($page->socials == 1)
        @include('components.share-scripts')
    @endif
@endpush
