<?php

namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Entities\Pagination;

class ArticlePaginationTableSeeder extends Seeder
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
                'on'  => 'articles',
                'for' => 'items'
            ],
            [
                'value'   => 9,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'articles',
                'for' => 'relates'
            ],
            [
                'value'   => 3,
                'user_id' => 1
            ]
        );
    }
}
