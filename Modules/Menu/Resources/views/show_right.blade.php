@section('right_menu')
    @isset($right_menu)
        @foreach($right_menu as $item_menu)
            <div class="right_menu_item" style="background-color: {{ $item_menu->color }}">
                <a href="{{ generateRoute($urlsPages, $item_menu) }}" class="d-flex align-items-center">
                    @if($item_menu->image)
                        <img src="{{ asset('storage/menus/xs/' . $item_menu->image) }}" class="me-2">
                    @elseif($item_menu->icon)
                        <i class="{{ $item_menu->icon }} me-3"></i>
                    @endif
                    <span>{{ $item_menu->trans->title }}</span>
                </a>
            </div>
        @endforeach
    @endisset
@show
