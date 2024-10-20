<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'user_1',
            'email' => 'user_1@test.com',
            'password' => bcrypt('password')
        ]);
        User::factory()->create([
            'name' => 'user_2',
            'email' => 'user_2@test.com',
            'password' => bcrypt('password')
        ]);
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password')
        ]);
    }
}
