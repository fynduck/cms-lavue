<?php

namespace Modules\Banner\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Banner\Entities\Banner;
use Modules\Banner\Entities\BannerShow;
use Modules\Banner\Entities\BannerTrans;

class BannerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        factory(BannerTrans::class, 1)->create()->each(function (BannerTrans $bannerTrans) {
            factory(BannerShow::class, 1)->create([
                'banner_id' => $bannerTrans->banner_id
            ]);
        });

        // $this->call("OthersTableSeeder");
    }
}
