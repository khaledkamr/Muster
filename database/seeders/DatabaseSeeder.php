<?php

namespace Database\Seeders;

use App\Models\Course;
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
        
        // $this->call([
        //     UserSeeder::class,
        // ]);
        // $departments = ['Computer Science', 'Mathematics', 'Physics', 'Information System', 'Artificial Intelligence'];
        // foreach ($departments as $department) {
        //     User::factory()->professor()->create(['department' => $department]);
        // }
        // User::factory(10)->create();
        // User::factory(10)->withParent()->create();
        // User::factory(5)->student()->create();
        // User::factory()->count(5)->professor()->create();
        User::factory()->count(5)->student()->withParent()->create();

        // Course::factory(10)->create();
        // $this->call(CourseSeeder::class);
    }
}
