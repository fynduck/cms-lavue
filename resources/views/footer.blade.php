<section class="container">
    <div class="row">
        @if(array_key_exists(\Modules\Menu\Entities\Menu::FOOTER_LINKS,$menus->toArray()))
            @foreach($menus[\Modules\Menu\Entities\Menu::FOOTER_LINKS] as $item)
                <div class="col-md-6{{ $loop->last ? ' col-lg-3' : '' }} mb-5 mb-sm-2">
                    <ul class="footer_links {{ $loop->last ? 'last' : '' }}">
                        <li class="title">
                            <a href="{{ generateRoute($item) }}">{{ $item->title }}</a>
                        </li>
                        @if($item->children->count())
                            <li>
                                <ul>
                                    @foreach($item->children->sortBy('sort') as $child)
                                        @if($child->trans()->where('active', 1)->lang()->exists())
                                            <li class=" item">
                                                <a href="{{ generateRoute($child) }}" class="link">
                                                    {{ $child->trans()->lang()->value('title') }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            @endforeach
        @endif
        <div class="col-md-6 col-lg-3 footer_contact mb-5 mb-md-2" itemscope itemtype="http://schema.org/Organization">
            <div class="mb-3 name_site">
                {{ $settings['name_site'] ?? '' }}
            </div>
            <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress" class="mb-3">
                <i class="icofont-google-map mr-3"></i>
                @if(array_key_exists('postal_code', $settings))
                    <span itemprop="postalCode">{{ $settings['postal_code'] }}</span>
                @endif
                @if(array_key_exists('city', $settings))
                    <span itemprop="addressLocality">{{ $settings['city'] }}</span>
                @endif
                @if(array_key_exists('street', $settings))
                    <span itemprop="streetAddress">{{ $settings['street'] }}</span>
                @endif
            </div>
            @if(array_key_exists('contact_phone', $settings))
                <div class="mb-3">
                    <a href="tel:{{ str_replace(' ', '', $settings['contact_phone']) }},">
                        <i class="icofont-phone mr-3"></i>
                        <span itemprop="telephone">{{ $settings['contact_phone'] }}</span>,
                    </a>
                </div>
            @endif
            @if(array_key_exists('contact_email', $settings))
                <a href="mailto: {{ $settings['contact_email'] }}">
                    <i class="icofont-envelope mr-3"></i> {!! $settings['contact_email'] !!}
                </a>
            @endif
            @if(!empty($socials))
                <div class="d-flex justify-content-around my-3">
                    @foreach($socials as $social)
                        <a href="{{ $social->url }}" class="socials-page" target="_blank" title="{{ $social->name }}">
                            <i class="{{ $social->class_icon }}"></i>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
<hr class="line">
@include('copyright')
