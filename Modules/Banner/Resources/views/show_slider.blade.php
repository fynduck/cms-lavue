@section('slider')
    @if(isset($bannersTop) && count($bannersTop) > 0)
        <div id="carousel-slide" class="carousel slide carousel-fade mx-auto" data-ride="carousel">
            <div class="carousel-inner">
                @foreach($bannersTop as $key => $banner)
                    <div class="carousel-item {{ $key == key($bannersTop) ? 'active' : '' }}" v-lazy-container="{ selector: 'img' }">
                        @if($banner->link)
                            <a href="{{ $banner->link }}" target="{{ $banner->target }}">
                                <img class="d-block w-100" data-src="{{ asset('storage/banners/lg/' . $banner->image) }}"
                                     data-loading="{{ asset('storage/banners/xs/' . $banner->image) }}" alt="{{ $banner->title }}">
                                @if(strip_tags($banner->description) != '')
                                    <div class="carousel-caption d-none d-lg-block">
                                        <div class="block">
                                            {!! $banner->description !!}
                                        </div>
                                    </div>
                                @endif
                            </a>
                        @else
                            <img class="d-block w-100" data-src="{{ asset('storage/banners/lg/' . $banner->image) }}"
                                 data-loading="{{ asset('storage/banners/xs/' . $banner->image) }}" alt="{{ $banner->title }}">
                            @if(strip_tags($banner->description) != '')
                                <div class="carousel-caption d-none d-lg-block">
                                    <div class="block">
                                        {!! $banner->description !!}
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
            @if(count($bannersTop) > 1)
                <a class="carousel-control-prev" href="#carousel-slide" role="button" data-slide="prev">
                    <i class="fas fa-chevron-left"></i>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carousel-slide" role="button" data-slide="next">
                    <i class="fas fa-chevron-right"></i>
                    <span class="sr-only">Next</span>
                </a>
            @endif
        </div>
    @endif
@show
