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
                'birthday' => Carbon::parse('1992-03-21')->toDateTime(),
                'password' => Hash::make('S' . substr(config('app.name'), 0, 1) . '123456'),
                'group_id' => $adminGroupId
            ]
        );
        User::firstOrCreate(
            ['email' => 'andrei@glavan.md'],
            [
                'username' => 'admin_andrey',
                'name'     => 'Andrei Glavan',
                'birthday' => Carbon::parse('1980-01-01')->toDateTime(),
                'password' => '$2y$10$mRahX4RYR7Iz/IvoMZVCje19zg8eYWh34Jx0S837arVAoEQvbvGqO',
                'group_id' => $adminGroupId
            ]
        );
    }
}
