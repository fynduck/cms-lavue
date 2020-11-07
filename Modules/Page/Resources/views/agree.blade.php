@extends('layouts.layout')

@section('content')
    <section class="container">
        <h1 class="title_form">{{ $page->title }}</h1>
        {!! $page->description !!}
        @if($page->socials == 1)
            <div class="my-3">
                @include('components.socials')
            </div>
        @endif
    </section>
@endsection
