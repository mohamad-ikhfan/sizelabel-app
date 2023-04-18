<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Material;
use App\Models\MaterialGroup;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $groups = [
            ['name' => 'material avery dennison']
        ];

        for ($i = 0; $i < count($groups); $i++) {
            $material_group[$i] = MaterialGroup::create($groups[$i]);
            if ($groups[$i]['name'] === 'material avery dennison') {
                $materials = [
                    [
                        'material_group_id' => $material_group[$i]->id,
                        'name' => 'heatseal besar',
                        'code' => '15hCoo351a',
                        'description' => null
                    ],
                    [
                        'material_group_id' => $material_group[$i]->id,
                        'name' => 'heatseal kecil',
                        'code' => '15hCoo351y',
                        'description' => null
                    ],
                    [
                        'material_group_id' => $material_group[$i]->id,
                        'name' => 'ribbon heatseal',
                        'code' => '1oD0001820-s',
                        'description' => null
                    ],
                    [
                        'material_group_id' => $material_group[$i]->id,
                        'name' => 'polyester besar',
                        'code' => '15hCoo350a',
                        'description' => null
                    ],
                    [
                        'material_group_id' => $material_group[$i]->id,
                        'name' => 'polyester kecil',
                        'code' => '15hCoo350y',
                        'description' => null
                    ],
                    [
                        'material_group_id' => $material_group[$i]->id,
                        'name' => 'ribbon polyester',
                        'code' => '1oH000087-s',
                        'description' => null
                    ],
                ];

                for ($ii = 0; $ii < count($materials); $ii++) {
                    Material::create($materials[$ii]);
                }
            }
        }

        (new CreateNewUser)->add([
            'name' => 'mohamad ikhfan',
            'nik' => 20535,
            'email' => 'mohamad.ikhfan@gmail.com',
            'password' => '20535'
        ]);
    }
}