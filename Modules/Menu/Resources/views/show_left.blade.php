@section('show_left')
    @if(isset($leftMenu) && count($leftMenu) > 0)
        @foreach($leftMenu as $item)
            <a class="dropdown-item wow fadeIn" href="{{ $item->link ? $item->link : '#' }}" data-wow-duration=".5{{ $loop->iteration + 1 }}s"
               target="{{ $item->target }}" {{ $item->nofollow ? 'nofollow' : '' }}>
                @if($item->image)
                    <img src="{{ asset('images/menus/30x30/' . $item->image) }}" alt="{{ $item->title }}" class="mr-2">
                @endif
                <span>{{ $item->title }}</span>
            </a>
        @endforeach
    @endif
@show