<?php

namespace Modules\Search\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Entities\Pagination;

class SeedPaginationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Pagination::firstOrCreate(
            [
                'on'  => 'search_page',
                'for' => 'items'
            ],
            [
                'value'   => 20,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'live_search',
                'for' => 'items'
            ],
            [
                'value'   => 10,
                'user_id' => 1
            ]
        );
    }
}
