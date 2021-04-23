<?php

namespace Modules\Search\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Language\Entities\Language;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageTrans;

class SearchDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(
            [
                SeedPageTableSeeder::class,
                SeedPaginationTableSeeder::class
            ]
        );
    }
}
