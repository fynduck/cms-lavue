<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Language\Entities\Language;
use Modules\Settings\Entities\Settings;

class SettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $languages = Language::all();
        foreach ($languages as $lang) {
            Settings::firstOrCreate(
                [
                    'key'   => 'name_site',
                    'value' => '',
                    'lang'  => $lang->id,
                ]
            );
        }
        Settings::firstOrCreate(
            [
                'key'   => 'contact_email',
                'value' => '',
                'lang'  => 0,
            ]
        );
        Settings::firstOrCreate(
            [
                'key'   => 'contact_phone',
                'value' => '',
                'lang'  => 0,
            ]
        );

         $this->call([
             SeedSocialsTableSeeder::class,
             PaginationTableSeeder::class
         ]);
    }
}
