@if(!empty($breadcrumbs))
    <div class="container">
        <ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
            @foreach($breadcrumbs as $key => $breadcrumb)
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    @if($breadcrumb['url'])
                        <a itemprop="item" href="{{ $breadcrumb['url'] }}">
                            <span itemprop="name">{{ $breadcrumb['title'] }}</span>
                        </a>
                    @else
                        <span itemprop="name">{{ $breadcrumb['title'] }}</span>
                    @endif
                    <meta itemprop="position" content="{{ $key+1 }}"/>
                </li>
            @endforeach
        </ol>
    </div>
@endif

