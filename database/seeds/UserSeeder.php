<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            "name" => "Yellow Club",
            "email" => "mid@yellowclub.com.co",
            "password" => "YC2019*"
        ]);
    }
}
