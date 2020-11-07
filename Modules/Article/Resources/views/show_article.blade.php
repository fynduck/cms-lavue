@extends('layouts.layout')
@section('content')
    <section class="container">
        <div class="row">
            <div class="col-lg-8">
                <figure v-lazy-container="{ selector: 'img', loading: '{{ asset("storage/articles/50/" . $item->image) }}'}">
                    <img data-src="{{ asset('storage/articles/xl/' . $item->image) }}" alt="{{ $item->title }}"
                         src="//via.placeholder.com/823x433" class="lazy-img">
                </figure>
                @if($item->socials == 1)
                    @include('components.socials')
                @endif
                <h1 class="title_form mb-3">{{ $item->title }}</h1>
                <div>{!! $item->description !!}</div>
                <div class="row my-4">
                    <div class="col-sm-6 my-3">
                        <time datetime="{{ $item->date->format('Y-m-d') }}"
                              class="inner_art_time">{{ $item->date->format('d.m.Y') }}</time>
                    </div>
                    <div class="col-sm-6 my-3 text-center text-sm-right">
                        <a href="{{ route('pages', $urlsPages[$page->method] ?? '') }}"
                           class="btn-custom">@lang('global.all_' . $page->method)</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                @if($relate_articles->count())
                    <h2 class="title_last_notes">@lang('global.other_articles')</h2>
                    <div class="row">
                        @foreach($relate_articles as $relate)
                            <div class="col-md-6 col-lg-12 mb-4">
                                <div class="article relate">
                                    <a href="{{ route('pages', [$urlsPages[$relate->type] ?? '', $relate->slug]) }}">
                                        <figure v-lazy-container="{ selector: 'img', loading: '{{ asset("storage/articles/50/" . $relate->image) }}'}">
                                            <img data-src="{{ asset('storage/articles/xs/' . $relate->image) }}"
                                                 alt="{{ $relate->title }}"
                                                 src="//via.placeholder.com/823x433" class="lazy-img">
                                        </figure>
                                    </a>
                                    <div class="title">
                                        <a href="{{ route('pages', [$urlsPages[$relate->type] ?? '', $relate->slug]) }}">
                                            {{ $relate->title }}
                                        </a>
                                    </div>
                                    <div class="desc">{{ $relate->short_desc ? strip_tags($relate->short_desc) : \Str::limit(strip_tags($relate->description), 160) }}</div>
                                    <div class="d-flex justify-content-between mt-4">
                                        <time pubdate
                                              datetime="{{ $relate->date->format('d.m.Y') }}">{{ $relate->date->format('d.m.Y') }}</time>
                                        <a href="{{ route('pages', [$urlsPages[$relate->type] ?? '', $relate->slug]) }}"
                                           class="further underline">@lang('global.further')</a>
                                    </div>
                                </div>
                                <hr class="lineW">
                            </div>
                        @endforeach
                    </div>
            </div>
            @endif
        </div>
        <comments source="{{ route('get-item-comments', ['article', $item->id]) }}" auth="{{ auth()->check() }}"></comments>
    </section>
@endsection
@push('scripts')
    @if($item->socials == 1)
        @include('components.share-scripts')
    @endif
@endpush
