@extends('layouts.layout')

@section('content')
    <section class="container">
        {!! $page->description !!}
        <hit-promo-products ref="hit-sales-products"
                            source="{{ route('get-param-products', ['type' => $page->method]) }}"
                            page="other"
                            cart="{{ route('pages', $urlsPages['cart'] ?? '#') }}"></hit-promo-products>
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
