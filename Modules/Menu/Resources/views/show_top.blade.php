@section('top_menu')
    <nav class="navbar navbar-dark navbar-expand-lg top_menu">
        <div class="container d-flex justify-content-between px-0">
            <div class="flex-fill">
                @isset($menus[\Modules\Menu\Entities\Menu::TOP_MENU])
                    <div class="collapse navbar-collapse" id="top_menu">
                        <ul class="navbar-nav mr-auto w-100 nav-fill">
                            @foreach($menus[\Modules\Menu\Entities\Menu::TOP_MENU] as $key => $top_menu)
                                <li class="nav-item">
                                    @if($top_menu->icon)<i class="{{ $top_menu->icon }}"></i>@endif
                                    <a class="nav-link" href="{{ $top_menu->children->count() ? '#' : generateRoute($top_menu) }}"
                                       target="{{ $top_menu->target }}" {{ $top_menu->nofollow ? 'nofollow' : '' }}
                                            {{ $top_menu->children->count() ? 'data-toggle=dropdown' : '' }}
                                            {{ $top_menu->color ? 'style=background-color:' . $top_menu->color : '' }}>
                                        {{ $top_menu->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endisset
            </div>
        </div>
    </nav>
@show
