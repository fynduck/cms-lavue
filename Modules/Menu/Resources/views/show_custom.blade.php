@section('custom_menu')
    <custom-menu source="{{ route('get-menu') }}" page_id="{{ $page->page_id }}" title="@lang('global.product_catalog')" position="{{ \Modules\Menu\Entities\Menu::CUSTOM_MENU }}"></custom-menu>
@show