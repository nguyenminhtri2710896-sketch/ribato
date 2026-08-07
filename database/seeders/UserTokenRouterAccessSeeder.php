<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTokenRouterAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            // Read routes
            'api.transaction.get-list' => 'read',
            'api.transaction.get-detail' => 'read',
            'api.bank.get-list' => 'read',
            'api.user-withdraw.get-list' => 'read',
            'api.account.get-balance' => 'read',
            'api.user-virtual-account.get-list' => 'read',
            'api.report.qrcode-revenue' => 'read',

            // Write routes
            'api.transaction.create-payment' => 'write',
            'api.transaction.create-qr-payment' => 'write',
            'api.user-withdraw.create' => 'write',
            'api.account.create-qr-payment' => 'write',
        ];

        foreach ($routes as $routeName => $permission) {
            \DB::table('user_token_router_access')->updateOrInsert(
                ['route_name' => $routeName],
                [
                    'permission' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
