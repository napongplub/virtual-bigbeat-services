<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "CMO",
            'email' => "dev@cmo-group.com",
            'password' => '$2y$10$FMEsEKPeTZ1Qvaryu068/OoAlHpyeQojkZYOVN8yHA/SS3qgMOHva',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
