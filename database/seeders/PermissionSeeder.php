<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'view-any-role'
            ],
            [
                'name' => 'view-role'
            ],
            [
                'name' => 'create-role'
            ],
            [
                'name' => 'update-role'
            ],
            [
                'name' => 'delete-role'
            ],
            [
                'name' => 'restore-role'
            ],
            [
                'name' => 'force-delete-role'
            ],

            [
                'name' => 'view-any-permission'
            ],
            [
                'name' => 'view-permission'
            ],
            [
                'name' => 'create-permission'
            ],
            [
                'name' => 'update-permission'
            ],
            [
                'name' => 'delete-permission'
            ],
            [
                'name' => 'restore-permission'
            ],
            [
                'name' => 'force-delete-permission'
            ],

            [
                'name' => 'view-any-user'
            ],
            [
                'name' => 'view-user'
            ],
            [
                'name' => 'create-user'
            ],
            [
                'name' => 'update-user'
            ],
            [
                'name' => 'delete-user'
            ],
            [
                'name' => 'restore-user'
            ],
            [
                'name' => 'force-delete-user'
            ],

            [
                'name' => 'view-any-shoe'
            ],
            [
                'name' => 'view-shoe'
            ],
            [
                'name' => 'create-shoe'
            ],
            [
                'name' => 'update-shoe'
            ],
            [
                'name' => 'delete-shoe'
            ],
            [
                'name' => 'restore-shoe'
            ],
            [
                'name' => 'force-delete-shoe'
            ],

            [
                'name' => 'view-any-loadplan'
            ],
            [
                'name' => 'view-loadplan'
            ],
            [
                'name' => 'create-loadplan'
            ],
            [
                'name' => 'update-loadplan'
            ],
            [
                'name' => 'delete-loadplan'
            ],
            [
                'name' => 'restore-loadplan'
            ],
            [
                'name' => 'force-delete-loadplan'
            ],
            [
                'name' => 'export-loadplan'
            ],
            [
                'name' => 'new-import-loadplan'
            ],

            [
                'name' => 'view-any-report-print'
            ],
            [
                'name' => 'view-report-print'
            ],
            [
                'name' => 'create-report-print'
            ],
            [
                'name' => 'update-report-print'
            ],
            [
                'name' => 'delete-report-print'
            ],
            [
                'name' => 'restore-report-print'
            ],
            [
                'name' => 'force-delete-report-print'
            ],
            [
                'name' => 'new-import-report'
            ],

            [
                'name' => 'view-any-schedule-print'
            ],
            [
                'name' => 'view-schedule-print'
            ],
            [
                'name' => 'create-schedule-print'
            ],
            [
                'name' => 'update-schedule-print'
            ],
            [
                'name' => 'delete-schedule-print'
            ],
            [
                'name' => 'restore-schedule-print'
            ],
            [
                'name' => 'force-delete-schedule-print'
            ],

            [
                'name' => 'new-schedule-print'
            ],
            [
                'name' => 'refresh-material'
            ],
            [
                'name' => 'sync-to-printed'
            ],
            [
                'name' => 'schedule-printing'
            ],

            [
                'name' => 'view-any-destroy-ribbon'
            ],
            [
                'name' => 'view-destroy-ribbon'
            ],
            [
                'name' => 'create-destroy-ribbon'
            ],
            [
                'name' => 'update-destroy-ribbon'
            ],
            [
                'name' => 'delete-destroy-ribbon'
            ],
            [
                'name' => 'restore-destroy-ribbon'
            ],
            [
                'name' => 'force-delete-destroy-ribbon'
            ],

            [
                'name' => 'view-any-material'
            ],
            [
                'name' => 'view-material'
            ],
            [
                'name' => 'create-material'
            ],
            [
                'name' => 'update-material'
            ],
            [
                'name' => 'delete-material'
            ],
            [
                'name' => 'restore-material'
            ],
            [
                'name' => 'force-delete-material'
            ],

            [
                'name' => 'view-any-material-stock'
            ],
            [
                'name' => 'view-material-stock'
            ],
            [
                'name' => 'create-material-stock'
            ],
            [
                'name' => 'update-material-stock'
            ],
            [
                'name' => 'delete-material-stock'
            ],
            [
                'name' => 'restore-material-stock'
            ],
            [
                'name' => 'force-delete-material-stock'
            ],

        ];

        for ($i = 0; $i < count($data); $i++) {
            if (Permission::where('name', $data[$i]['name'])->count() == 0) {
                Permission::create([
                    'name' => $data[$i]['name'],
                    'guard_name' => 'web'
                ]);
            }
        }

        $role = Role::where('name', 'super-admin')->first();
        if (!$role) {
            $role = Role::create(['name' => 'super-admin']);
        }
        $role->givePermissionTo(Permission::all());
    }
}