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
        // First run roles and permissions seeder
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Create test users with roles
        $testAdmin = User::create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
            'password' => bcrypt('passer123'),
            'role' => 'admin'
        ]);
        $testAdmin->assignRole('admin');

        $testClient = User::create([
            'name' => 'Test Client',
            'email' => 'testclient@example.com',
            'password' => bcrypt('passer123'),
            'role' => 'client'
        ]);
        $testClient->assignRole('client');

        // Create additional users and assign them the client role
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('client');
        });

        // Then seed burgers and commands
        $this->call([
            BurgerSeeder::class,
            CommandSeeder::class,
        ]);
    }
}
