<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $objUserGroup =  \DB::table('user_groups')->where("id", 1)->first();
        if (!$objUserGroup) {
            \DB::table('user_groups')->insert([
                "id" => 1,
                'name' => "Quản trị viên",
                'aliat' => "Admin",
                'description' => "Quản trị viên",
            ]);
        }

        $objUserGroup =  \DB::table('user_groups')->where("id", 2)->first();
        if (!$objUserGroup) {
            \DB::table('user_groups')->insert([
                "id" => 2,
                'name' => "Nhân viên quản trị",
                'aliat' => "mod",
                'description' => "Nhân viên quản trị",
            ]);
        }

        $objUserGroup =  \DB::table('user_groups')->where("id", 3)->first();
        if (!$objUserGroup) {
            \DB::table('user_groups')->insert([
                "id" => 3,
                'name' => "Khách hàng",
                'aliat' => "member",
                'description' => "Khách hàng",
            ]);
        }
    }
}
