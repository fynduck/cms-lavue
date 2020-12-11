<?php

namespace Modules\User\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\User;
use Modules\UserGroup\Entities\UserGroup;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $adminGroupId = UserGroup::where('name', 'Admin')->value('id');

        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'username' => 'admin',
                'name'     => 'admin',
                'password' => '$2y$10$jAfc8ZkLs1J2aKoKUtV06OO1RpRnl80L88cCSOLyWNixfhjSRgE9W',
                'group_id' => $adminGroupId
            ]
        );
        User::firstOrCreate(
            ['email' => 'andrei@glavan.md'],
            [
                'username' => 'admin_andrey',
                'name'     => 'Andrei Glavan',
                'password' => '$2y$10$mRahX4RYR7Iz/IvoMZVCje19zg8eYWh34Jx0S837arVAoEQvbvGqO',
                'group_id' => $adminGroupId
            ]
        );
    }
}
