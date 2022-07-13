<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'hello',// 隨機產生str，這些都是抽象函數
            'email' => 'shindt1969@gmail.com',
            'password' => bcrypt('1234'),
        ]);
    }
}
