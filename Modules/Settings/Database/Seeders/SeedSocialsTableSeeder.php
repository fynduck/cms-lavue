<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Entities\Social;

class SeedSocialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Social::firstOrCreate(
            [
                'name'       => 'facebook',
                'url'        => '//facebook.com',
                'class_icon' => 'icofont-facebook',
                'priority'   => 1,
            ]
        );
        Social::firstOrCreate(
            [
                'name'       => 'vk',
                'url'        => '//vk.com',
                'class_icon' => 'icofont-vk',
                'priority'   => 3,
            ]
        );
        Social::firstOrCreate(
            [
                'name'       => 'odnoklasniki',
                'url'        => '//ok.ru',
                'class_icon' => 'odnoklassniki',
                'priority'   => 0,
            ]
        );
        Social::firstOrCreate(
            [
                'name'       => 'pinterest',
                'url'        => '//www.pinterest.com',
                'class_icon' => 'pinterest-p',
                'priority'   => 0,
            ]
        );
        Social::firstOrCreate(
            [
                'name'       => 'instagram',
                'url'        => '//www.instagram.com',
                'class_icon' => 'icofont-instagram',
                'priority'   => 2,
            ]
        );
    }
}
