<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Entities\Pagination;

class PaginationTableSeeder extends Seeder
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
                'value'   => 3,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'articles',
                'for' => 'relate_articles'
            ],
            [
                'value'   => 4,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'promotions',
                'for' => 'items'
            ],
            [
                'value'   => 2,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'category',
                'for' => 'items'
            ],
            [
                'value'   => 6,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'product',
                'for' => 'relate_products'
            ],
            [
                'value'   => 6,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'other',
                'for' => 'products'
            ],
            [
                'value'   => 6,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'home_promo',
                'for' => 'products'
            ],
            [
                'value'   => 6,
                'user_id' => 1
            ]
        );

        Pagination::firstOrCreate(
            [
                'on'  => 'home_hit',
                'for' => 'products'
            ],
            [
                'value'   => 6,
                'user_id' => 1
            ]
        );
    }
}
