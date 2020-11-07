@extends('layouts.layout')

@section('content')
    <section class="container-2">
        <h1 class="title_form mb-3">{{ $page->title }}</h1>
        {!! $page->description !!}
        @if($page->socials == 1)
            @include('components.socials')
        @endif
    </section>
@endsection
@push('scripts')
    @if($page->socials == 1)
        @include('components.share-scripts')
    @endif
@endpush
