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
        $pageArticles = Page::firstOrCreate(['method' => 'articles', 'module' => 'Article']);

        $languages = Language::all();
        foreach ($languages as $lang) {
            PageTrans::firstOrCreate(
                [
                    'page_id' => $home->id,
                    'title'   => 'Home ' . $lang->name,
                    'slug'    => '',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageNotFound->id,
                    'title'   => '404 Not Found ' . $lang->name,
                    'slug'    => '',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageContacts->id,
                    'title'   => 'Contacts ' . $lang->name,
                    'slug'    => 'contacts',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageAbout->id,
                    'title'   => 'О компании ' . $lang->name,
                    'slug'    => 'about',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageArticles->id,
                    'title'   => 'Публикации ' . $lang->name,
                    'slug'    => 'articles',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
        }
    }
}
