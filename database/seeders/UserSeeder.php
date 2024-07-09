<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Mohamad Ikhfan',
                'nik' => '20535',
                'phone' => '083873126222',
                'email' => 'label.ppic@pai.pratama.net',
                'password' => bcrypt('20535')
            ],
            [
                'name' => 'N Tri Dayanti',
                'nik' => '43682',
                'phone' => null,
                'email' => 'tri.ppic@pai.pratama.net',
                'password' => bcrypt('43682')
            ],
        ];

        for ($i = 0; $i < count($data); $i++) {
            $user = User::where('nik', $data[$i]['nik'])->first();
            if (!$user) {
                if ($data[$i]['nik'] == '20535') {
                    $user = User::create($data[$i]);
                    $user->assignRole('super-admin');
                } else {
                    User::create($data[$i]);
                }
            } else {
                if ($data[$i]['nik'] == '20535') {
                    $user->assignRole('super-admin');
                } else {
                    $user->givePermissionTo(['view-any-schedule-print', 'schedule-printing']);
                }
            }
        }
    }
}