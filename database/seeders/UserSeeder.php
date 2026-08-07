<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $objUser =  \DB::table('users')->where("group_id", 1)->first();
        if (!$objUser) {
            \DB::table('users')->insert([
                'group_id' => 1,
                "first_name" => "Admin",
                "last_name" => "Admin",
                "fullname" => "Admin",
                'email' => "admin@gmail.com",
                'password' => Hash::make('123123'),
            ]);
        }

        $objUser =  \DB::table('users')->where("group_id", 2)->first();
        if (!$objUser) {
            \DB::table('users')->insert([
                'group_id' => 2,
                "first_name" => "Mod",
                "last_name" => "Mod",
                "fullname" => "Mod",
                'email' => "mod@gmail.com",
                'password' => Hash::make('123123'),
            ]);
        }
        $objUser =  \DB::table('users')->where("group_id", 3)->first();
        if (!$objUser) {
            \DB::table('users')->insert([
                'group_id' => 3,
                "first_name" => "Thành Viên",
                "last_name" => "Thành Viên",
                "fullname" => "Thành Viên",
                'email' => "member@gmail.com",
                'password' => Hash::make('123123'),
            ]);
        }
    }
}
