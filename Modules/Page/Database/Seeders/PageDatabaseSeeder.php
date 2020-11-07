<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Language\Entities\Language;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;

class PageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $home = Page::firstOrCreate(['method' => 'home']);
        $pageNotFound = Page::firstOrCreate(['method' => 'not_found']);
        $pageContacts = Page::firstOrCreate(['method' => 'contacts', 'module' => 'Page']);
        $pageAbout = Page::firstOrCreate(['method' => 'about', 'module' => 'Page']);
        $pageNews = Page::firstOrCreate(['method' => 'news', 'module' => 'Article']);
        $pageArticles = Page::firstOrCreate(['method' => 'articles', 'module' => 'Article']);
        $pagePromotions = Page::firstOrCreate(['method' => 'promotions', 'module' => 'Article']);
        $pageCategories = Page::firstOrCreate(['method' => 'catalog', 'module' => 'Category']);
        $pageHits = Page::firstOrCreate(['method' => 'hits', 'module' => 'Page']);
        $pageSales = Page::firstOrCreate(['method' => 'sales', 'module' => 'Page']);

        $languages = Language::all();
        foreach ($languages as $lang) {
            PageTrans::firstOrCreate(
                [
                    'page_id' => $home->id,
                    'title'   => 'Home',
                    'slug'    => '',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageNotFound->id,
                    'title'   => '404 Not Found',
                    'slug'    => '',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageContacts->id,
                    'title'   => 'contacts',
                    'slug'    => 'contacts',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageNews->id,
                    'title'   => 'Новости',
                    'slug'    => 'news',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageAbout->id,
                    'title'   => 'О компании',
                    'slug'    => 'about',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageArticles->id,
                    'title'   => 'Публикации',
                    'slug'    => 'articles',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pagePromotions->id,
                    'title'   => 'Акции',
                    'slug'    => 'promotions',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageCategories->id,
                    'title'   => 'Каталог',
                    'slug'    => 'catalog',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageHits->id,
                    'title'   => 'Хиты',
                    'slug'    => 'hits',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageSales->id,
                    'title'   => 'Скитки',
                    'slug'    => 'sales',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
        }
    }
}
