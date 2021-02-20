@extends('layouts.layout')
@section('content')
    <section class="container">
        <div class="row">
            <div class="col-lg-8">
                <article>
                    <h1 class="title_form mb-3">{{ $item->title }}</h1>
                    <figure>
                        <img src="{{ asset('storage/articles/lg/' . $item->image) }}" alt="{{ $item->title }}">
                    </figure>
                    <div class="mb-3 d-flex justify-content-between">
                        @if($item->socials == 1)
                            @include('components.socials')
                        @endif
                        <div class="date">
                            {{ $item->date->format('d.m.Y') }}
                        </div>
                    </div>
                    {!! $item->description !!}
                </article>
                <div class="row">
                    <div class="col-md-6 mb-4 promo_inner">
                        <div class="promo_time">
                            @if(is_null($item->date_to) || $item->date_to->isFuture())
                                <div class="title">@lang('global.before_end')</div>
                                @if(is_null($item->date_to))
                                    <div class="indefinitely">@lang('global.indefinitely')</div>
                                @else
                                    <time-down :end-time="{{ $item->date_to->timestamp }}"
                                               :second="false"
                                               :labels="{{ $labels }}"
                                               finish-txt="@lang('global.finished')"></time-down>
                                @endif
                            @else
                                <div class="title">@lang('global.before_end')</div>
                                <div class="finished">@lang('global.finished')</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-center justify-content-md-end align-items-center mb-4">
                        <a href="{{ route('pages', $urlsPages[$item->type] ?? '#') }}" class="btn-custom">
                            {{ trans('global.back_to_' . $item->type) }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                @if(checkModule('Menu'))
                    <custom-menu
                            source="{{ route('get-menu', ['position' => \Modules\Menu\Entities\Menu::CUSTOM_MENU, 'page_id' => $page->page_id]) }}"></custom-menu>
                @endif
            </div>
        </div>
    </section>
    @if($item->getProducts()->exists() && (is_null($item->date_to) || $item->date_to->isFuture()))
        <section class="container my-5">
            <h2 class="title_char_desc mb-4">
                @lang('global.product_on_sale')
            </h2>
            <relate-products id="promo-products" source="{{ route('get-param-products', ['promo_id' => $item->id]) }}"
                             cart="{{ route('pages', $urlsPages['cart'] ?? '#') }}"
                             page="other"></relate-products>
        </section>
    @endif
@endsection
@push('scripts')
    @if($item->socials == 1)
        @include('components.share-scripts')
    @endif
@endpush
