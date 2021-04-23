<?php

namespace Modules\Search\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Language\Entities\Language;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;

class SeedPageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $pageSearch = Page::firstOrCreate(['method' => 'search', 'module' => 'Search']);

        $languages = Language::all();
        foreach ($languages as $lang) {
            PageTrans::firstOrCreate(
                [
                    'page_id' => $pageSearch->id,
                    'title'   => 'Search',
                    'slug'    => 'search',
                    'lang_id' => $lang->id,
                    'active'  => 1
                ]
            );
        }
    }
}
