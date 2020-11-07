<?php

use Faker\Generator as Faker;

$factory->define(\Modules\Redirect\Entities\Redirect::class, function (Faker $faker) {
    return [
        'from'        => 'a',
        'to'          => 'b',
        'status_code' => 301,
        'status'      => 1
    ];
});
