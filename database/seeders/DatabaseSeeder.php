<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        (new CreateNewUser)->add([
            'name' => 'mohamad ikhfan',
            'nik' => 20535,
            'email' => 'mohamad.ikhfan@gmail.com',
            'password' => '20535'
        ]);
    }
}
