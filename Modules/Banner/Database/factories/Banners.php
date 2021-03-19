<?php

use Faker\Generator as Faker;

$factory->define(\Modules\Banner\Entities\Banner::class, function (Faker $faker) {
    return [
        'page_id'   => \Modules\Page\Entities\Page::value('id'),
        'sort'      => 0,
        'target'    => '_self',
        'type_page' => 'page',
        'type'      => 'top',
        'date_from' => now(),
        'date_to'   => now()->addDays(10)
    ];
});

$factory->define(\Modules\Banner\Entities\BannerTrans::class, function (Faker $faker) {
    return [
        'banner_id'   => factory(\Modules\Banner\Entities\Banner::class)->create()->id,
        'title'       => $faker->title,
        'description' => $faker->paragraphs(rand(2, 20), true),
        'lang'        => 1,
        'status'      => 1,
    ];
});

$factory->define(\Modules\Banner\Entities\BannerShow::class, function (Faker $faker) {
    return [
        'type_page' => 'page',
        'page_id'   => \Modules\Page\Entities\Page::value('id'),
    ];
});
