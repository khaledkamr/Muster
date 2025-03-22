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


        $departments = [
            'General Education',
            'Computer Science',
            'Artificial Intelligence',
            'Information System',
        ];
        foreach ($departments as $department) {
            User::factory()->count(2)->professor()->create(['department' => $department]);
        }        

        User::factory()->count(5)->student()->create(['year' => 'freshman', 'major' => null]);
        User::factory()->count(5)->student()->create(['year' => 'sophomore', 'major' => null]);
        User::factory()->count(5)->student()->withParent()->create(['year' => 'junior', 'major' => 'Computer Science']);
        User::factory()->count(5)->student()->withParent()->create(['year' => 'junior', 'major' => 'Artificial Intelligence']);
        User::factory()->count(5)->student()->withParent()->create(['year' => 'junior', 'major' => 'Information System']);
        User::factory()->count(5)->student()->withParent()->create(['year' => 'senior', 'major' => 'Computer Science']);
        User::factory()->count(5)->student()->withParent()->create(['year' => 'senior', 'major' => 'Artificial Intelligence']);
        User::factory()->count(5)->student()->withParent()->create(['year' => 'senior', 'major' => 'Information System']);

        $this->call(CourseSeeder::class);
        $this->call(EnrollmentSeeder::class);
        $this->call(AssignmentSeeder::class);
        $this->call(GradeSeeder::class);
        $this->call(AttendanceSeeder::class);
        $this->call(CurrentSemesterSeeder::class);
    }
}
