@if(checkModule('Menu'))
    <top-menu app-name="{{ config('app.name') }}"
              source="{{ route('get-menu', ['position' => \Modules\Menu\Entities\Menu::TOP_MENU]) }}"></top-menu>
@endif
@yield('header')
