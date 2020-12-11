<?php

namespace Modules\Language\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Language\Entities\Language;

class LanguageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Language::create([
            'id'          => 1,
            'country_iso' => 'RU',
            'slug'        => 'ru',
            'name'        => 'Русский',
            'active'      => 1,
            'default'     => 1,
            'priority'        => 1,
        ]);
    }
}
