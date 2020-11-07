<?php

namespace Modules\UserGroup\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\UserGroup\Entities\UserGroup;

class UserGroupDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        UserGroup::firstOrCreate(
            [
                'name'  => UserGroup::ADMIN,
                'admin' => 1,
            ]
        );

        UserGroup::firstOrCreate(
            [
                'name'  => UserGroup::USERS,
                'admin' => 0,
            ]
        );
    }
}
