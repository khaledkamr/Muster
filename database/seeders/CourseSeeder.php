<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Computer Science' => 'CS',
            'Artificial Intelligence' => 'AI',
            'Mathematics' => 'MATH',
            'Physics' => 'PHY',
            'Information System' => 'IS',
        ];

        $courseNames = [
            'Computer Science' => ['Introduction to Programming', 'Object Oriented Programming', 'Data Structures', 'Algorithms', 'Operating Systems', 'Software Engineering'],
            'Artificial Intelligence' => ['Machine Learning I', 'Neural Networks', 'AI Ethics', 'Natural Language Processing', 'Computer Vision', 'Data Science'],
            'Mathematics' => ['Calculus I', 'Linear Algebra', 'Discrete Mathematics', 'Probability and Statistics', 'Differential Equations'],
            'Physics' => ['Mechanics', 'Electromagnetism', 'Quantum Physics', 'Thermodynamics', 'Optics'],
            'Information System' => ['System Analysis', 'Information Security', 'Database Management I', 'Computer Networks I', 'Enterprise Systems'],
        ];

        $electiveCourseNames = [
            'Computer Science' => ['Web Development', 'Mobile Development', 'Game Development', 'Cybersecurity', 'Cloud Computing'],
            'Artificial Intelligence' => ['Machine Learning II', 'Deep Learning', 'AI Applications', 'AI in Robotics', 'AI in Healthcare'],
            'Mathematics' => ['Calculus II', 'Numerical Analysis', 'Graph Theory', 'Topology', 'Complex Analysis'],
            'Physics' => ['Quantum Mechanics', 'Statistical Mechanics', 'Astrophysics', 'Particle Physics', 'Nuclear Physics'],
            'Information System' => ['Database Management II', 'Computer Networks II', 'Web Security', 'Information Retrieval', 'Big Data'],
        ];

        $codeNumbers = [
            'Computer Science' => 101,
            'Artificial Intelligence' => 101,
            'Mathematics' => 101,
            'Physics' => 101,
            'Information System' => 101,
        ];

        foreach ($departments as $department => $codePrefix) {
            foreach ($courseNames[$department] as $name) {
                $code = $codePrefix . $codeNumbers[$department];
                Course::factory()->forCourse($department, $name, $code, 'compulsory')->create();
                $codeNumbers[$department]++; 
            }
            foreach ($electiveCourseNames[$department] as $name) {
                $code = $codePrefix . $codeNumbers[$department];
                Course::factory()->forCourse($department, $name, $code, 'elective')->create();
                $codeNumbers[$department]++; 
            }
        }
    }
}
