@extends('layouts.layout')
@section('content')
    <section class="py-5 bg-gray">
        <div class="container">
            <h2 class="title_form">{{ $page->title }}</h2>
            @if(strip_tags($page->description))
                {!! $page->description !!}
            @endif
        </div>
    </section>
    <articles source="{{ route('get-articles') }}" type="{{ \Modules\Article\Entities\Article::ARTICLES }}"></articles>
    @if(strip_tags($page->description_footer))
        <section class="container">
            {!! $page->description_footer !!}
        </section>
    @endif
@stop