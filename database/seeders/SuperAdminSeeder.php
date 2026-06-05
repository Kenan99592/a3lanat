<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'super-admin-tenant'],
            [
                'name'   => 'Meta Ads Platform',
                'slug'   => 'super-admin-tenant',
                'status' => 'active',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@metaads.com'],
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Super Admin',
                'email'     => 'admin@metaads.com',
                'password'  => Hash::make('Admin@123456'),
                'role'      => 'super_admin',
                'status'    => 'active',
            ]
        );

        $tenant->tenantUsers()->updateOrCreate(
            ['user_id' => $admin->id],
            ['role' => 'owner', 'is_active' => true]
        );
    }
}
