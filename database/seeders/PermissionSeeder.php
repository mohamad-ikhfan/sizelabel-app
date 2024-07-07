<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
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
        ];

        for ($i = 0; $i < count($data); $i++) {
            if (Permission::where('name', $data[$i]['name'])->count() == 0) {
                Permission::create([
                    'name' => $data[$i]['name'],
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}
