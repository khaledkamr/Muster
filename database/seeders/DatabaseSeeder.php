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
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        $this->call([
            UserSeeder::class,
        ]);

        // User::factory(10)->create();
        // User::factory(10)->withParent()->create();
        // User::factory(5)->student()->create();
        User::factory()->count(5)->professor()->create();
        User::factory()->count(5)->student()->withParent()->create();
    }
}
