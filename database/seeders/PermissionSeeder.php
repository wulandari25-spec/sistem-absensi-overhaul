<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'staffs.view',
            'staffs.create',
            'staffs.edit',
            'staffs.delete',

            'geofence.view',
            'geofence.manage',

            'reports.view',
            'reports.export',

            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}