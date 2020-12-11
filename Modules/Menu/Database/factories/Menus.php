<?php

use Faker\Generator as Faker;

$factory->define(\Modules\Menu\Entities\Menu::class, function (Faker $faker) {
    $parentIds = \Modules\Menu\Entities\Menu::where('position', 'footer')->pluck('id');
    return [
        'parent_id' => $parentIds->count() > 5 ? array_random($parentIds->toArray()) : null,
        'target'    => '_self',
        'color'     => null,
        'type_page' => 'page',
        'page_id'   => 1,
        'image'     => null,
        'icon'      => null,
        'position'  => 'footer',
        'nofollow'  => false,
        'priority'  => 0,
    ];
});

$factory->define(\Modules\Menu\Entities\MenuTrans::class, function (Faker $faker) {
    return [
        'menu_id'     => factory(\Modules\Menu\Entities\Menu::class)->create()->id,
        'title'       => $faker->title,
        'link'        => '',
        'description' => $faker->paragraphs(rand(2, 20), true),
        'lang'        => 1,
        'status'      => 1,
    ];
});
