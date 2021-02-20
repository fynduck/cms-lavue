@section('main_menu')
    <main-menu source="{{ route('get-menu') }}" page_id="{{ $page->page_id }}" title="@lang('global.product_catalog')"
               position="{{ \Modules\Menu\Entities\Menu::MAIN_MENU }}"/>
@show
