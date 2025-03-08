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
        // Course::truncate(); 

        $departments = [
            'Computer Science' => 'CS',
            'Artificial Intelligence' => 'AI',
            'Information System' => 'IS',
        ];

        $generalCourses = [
            'First Year, First Semester' => [
                ['name' => 'Introduction to College Writing', 'difficulty' => 'easy'],
                ['name' => 'Mathematics Fundamentals', 'difficulty' => 'medium'],
                ['name' => 'General Physics I', 'difficulty' => 'medium'],
                ['name' => 'Introduction to Programming', 'difficulty' => 'easy'],
                ['name' => 'World History', 'difficulty' => 'easy'],
                ['name' => 'Critical Thinking', 'difficulty' => 'medium'],
            ],
            'First Year, Second Semester' => [
                ['name' => 'College Writing II', 'difficulty' => 'medium'],
                ['name' => 'Calculus I', 'difficulty' => 'medium'],
                ['name' => 'General Physics II', 'difficulty' => 'medium'],
                ['name' => 'Programming Basics', 'difficulty' => 'medium'],
                ['name' => 'Introduction to Psychology', 'difficulty' => 'easy'],
                ['name' => 'Environmental Science', 'difficulty' => 'easy'],
            ],
            'Second Year, First Semester' => [
                ['name' => 'Technical Writing', 'difficulty' => 'medium'],
                ['name' => 'Calculus II', 'difficulty' => 'hard'],
                ['name' => 'General Chemistry I', 'difficulty' => 'medium'],
                ['name' => 'Data Structures', 'difficulty' => 'medium'],
                ['name' => 'Economics Basics', 'difficulty' => 'easy'],
                ['name' => 'Statistics I', 'difficulty' => 'medium'],
            ],
            'Second Year, Second Semester' => [
                ['name' => 'Professional Communication', 'difficulty' => 'medium'],
                ['name' => 'Linear Algebra', 'difficulty' => 'hard'],
                ['name' => 'General Chemistry II', 'difficulty' => 'medium'],
                ['name' => 'Algorithms', 'difficulty' => 'hard'],
                ['name' => 'Sociology Basics', 'difficulty' => 'easy'],
                ['name' => 'Statistics II', 'difficulty' => 'medium'],
            ],
        ];

        $geCode = 101;
        foreach ($generalCourses as $semester => $courses) {
            $semesterValue = strpos($semester, 'First Semester') !== false ? 'first' : 'second';
            foreach ($courses as $course) {
                Course::factory()->forCourse(
                    'General Education',
                    $course['name'],
                    "GE$geCode",
                    $semesterValue,
                    'compulsory',
                    $course['difficulty']
                )->create();
                $geCode++;
            }
        }

        $majorCourses = [
            'Computer Science' => [
                'compulsory' => [
                    'Third Year, First Semester' => [
                        ['name' => 'Operating Systems', 'difficulty' => 'hard'],
                        ['name' => 'Database Systems', 'difficulty' => 'medium'],
                        ['name' => 'Software Engineering I', 'difficulty' => 'medium'],
                        ['name' => 'Computer Networks', 'difficulty' => 'hard'],
                    ],
                    'Third Year, Second Semester' => [
                        ['name' => 'Software Engineering II', 'difficulty' => 'hard'],
                        ['name' => 'Web Development', 'difficulty' => 'medium'],
                        ['name' => 'Systems Programming', 'difficulty' => 'hard'],
                        ['name' => 'Computer Architecture', 'difficulty' => 'medium'],
                    ],
                    'Fourth Year, First Semester' => [
                        ['name' => 'Distributed Systems', 'difficulty' => 'hard'],
                        ['name' => 'Security Fundamentals', 'difficulty' => 'medium'],
                        ['name' => 'Capstone Project I', 'difficulty' => 'hard'],
                        ['name' => 'Advanced Algorithms', 'difficulty' => 'hard'],
                    ],
                    'Fourth Year, Second Semester' => [
                        ['name' => 'Cloud Computing', 'difficulty' => 'medium'],
                        ['name' => 'Capstone Project II', 'difficulty' => 'hard'],
                        ['name' => 'Mobile Development', 'difficulty' => 'medium'],
                        ['name' => 'Parallel Computing', 'difficulty' => 'hard'],
                    ],
                ],
                'elective' => [
                    'Third Year, First Semester' => [
                        ['name' => 'Game Development', 'difficulty' => 'medium'],
                        ['name' => 'Cybersecurity Basics', 'difficulty' => 'medium'],
                    ],
                    'Third Year, Second Semester' => [
                        ['name' => 'AI Fundamentals', 'difficulty' => 'hard'],
                        ['name' => 'Big Data Analytics', 'difficulty' => 'medium'],
                    ],
                    'Fourth Year, First Semester' => [
                        ['name' => 'Machine Learning', 'difficulty' => 'hard'],
                        ['name' => 'Blockchain Technology', 'difficulty' => 'medium'],
                    ],
                    'Fourth Year, Second Semester' => [
                        ['name' => 'IoT Systems', 'difficulty' => 'medium'],
                        ['name' => 'Advanced Cybersecurity', 'difficulty' => 'hard'],
                    ],
                ],
            ],
            'Artificial Intelligence' => [
                'compulsory' => [
                    'Third Year, First Semester' => [
                        ['name' => 'Machine Learning I', 'difficulty' => 'hard'],
                        ['name' => 'Neural Networks', 'difficulty' => 'hard'],
                        ['name' => 'Probability for AI', 'difficulty' => 'medium'],
                        ['name' => 'Data Science I', 'difficulty' => 'medium'],
                    ],
                    'Third Year, Second Semester' => [
                        ['name' => 'Machine Learning II', 'difficulty' => 'hard'],
                        ['name' => 'Computer Vision', 'difficulty' => 'hard'],
                        ['name' => 'Natural Language Processing', 'difficulty' => 'medium'],
                        ['name' => 'Data Science II', 'difficulty' => 'medium'],
                    ],
                    'Fourth Year, First Semester' => [
                        ['name' => 'Deep Learning', 'difficulty' => 'hard'],
                        ['name' => 'AI Ethics', 'difficulty' => 'medium'],
                        ['name' => 'Reinforcement Learning', 'difficulty' => 'hard'],
                        ['name' => 'AI Project I', 'difficulty' => 'hard'],
                    ],
                    'Fourth Year, Second Semester' => [
                        ['name' => 'Advanced AI Techniques', 'difficulty' => 'hard'],
                        ['name' => 'AI Project II', 'difficulty' => 'hard'],
                        ['name' => 'AI in Robotics', 'difficulty' => 'medium'],
                        ['name' => 'Generative Models', 'difficulty' => 'hard'],
                    ],
                ],
                'elective' => [
                    'Third Year, First Semester' => [
                        ['name' => 'AI in Healthcare', 'difficulty' => 'medium'],
                        ['name' => 'Time Series Analysis', 'difficulty' => 'medium'],
                    ],
                    'Third Year, Second Semester' => [
                        ['name' => 'Speech Recognition', 'difficulty' => 'hard'],
                        ['name' => 'Predictive Analytics', 'difficulty' => 'medium'],
                    ],
                    'Fourth Year, First Semester' => [
                        ['name' => 'AI Security', 'difficulty' => 'hard'],
                        ['name' => 'Expert Systems', 'difficulty' => 'medium'],
                    ],
                    'Fourth Year, Second Semester' => [
                        ['name' => 'Autonomous Systems', 'difficulty' => 'hard'],
                        ['name' => 'AI in Gaming', 'difficulty' => 'medium'],
                    ],
                ],
            ],
            'Information System' => [
                'compulsory' => [
                    'Third Year, First Semester' => [
                        ['name' => 'Database Management I', 'difficulty' => 'medium'],
                        ['name' => 'System Analysis', 'difficulty' => 'hard'],
                        ['name' => 'Network Security', 'difficulty' => 'hard'],
                        ['name' => 'Information Systems Design', 'difficulty' => 'medium'],
                    ],
                    'Third Year, Second Semester' => [
                        ['name' => 'Database Management II', 'difficulty' => 'medium'],
                        ['name' => 'Enterprise Systems', 'difficulty' => 'hard'],
                        ['name' => 'Web Development', 'difficulty' => 'medium'],
                        ['name' => 'IT Project Management', 'difficulty' => 'hard'],
                    ],
                    'Fourth Year, First Semester' => [
                        ['name' => 'Cloud Infrastructure', 'difficulty' => 'hard'],
                        ['name' => 'Data Warehousing', 'difficulty' => 'medium'],
                        ['name' => 'Capstone Project I', 'difficulty' => 'hard'],
                        ['name' => 'Cybersecurity', 'difficulty' => 'hard'],
                    ],
                    'Fourth Year, Second Semester' => [
                        ['name' => 'Big Data Systems', 'difficulty' => 'hard'],
                        ['name' => 'Capstone Project II', 'difficulty' => 'hard'],
                        ['name' => 'IT Governance', 'difficulty' => 'medium'],
                        ['name' => 'Advanced Networking', 'difficulty' => 'hard'],
                    ],
                ],
                'elective' => [
                    'Third Year, First Semester' => [
                        ['name' => 'Business Intelligence', 'difficulty' => 'medium'],
                        ['name' => 'Mobile App Development', 'difficulty' => 'medium'],
                    ],
                    'Third Year, Second Semester' => [
                        ['name' => 'Blockchain Applications', 'difficulty' => 'hard'],
                        ['name' => 'E-Commerce Systems', 'difficulty' => 'medium'],
                    ],
                    'Fourth Year, First Semester' => [
                        ['name' => 'IoT Security', 'difficulty' => 'hard'],
                        ['name' => 'Health Informatics', 'difficulty' => 'medium'],
                    ],
                    'Fourth Year, Second Semester' => [
                        ['name' => 'AI in IS', 'difficulty' => 'hard'],
                        ['name' => 'Digital Transformation', 'difficulty' => 'medium'],
                    ],
                ],
            ],
        ];

        foreach ($departments as $department => $codePrefix) {
            $code = 301; 
            foreach ($majorCourses[$department]['compulsory'] as $semester => $courses) {
                $semesterValue = strpos($semester, 'First Semester') !== false ? 'first' : 'second';
                foreach ($courses as $course) {
                    Course::factory()->forCourse(
                        $department,
                        $course['name'],
                        "$codePrefix$code",
                        $semesterValue,
                        'compulsory',
                        $course['difficulty']
                    )->create();
                    $code++;
                }
            }
            $electiveCode = 301;
            foreach ($majorCourses[$department]['elective'] as $semester => $courses) {
                $semesterValue = strpos($semester, 'First Semester') !== false ? 'first' : 'second';
                foreach ($courses as $course) {
                    Course::factory()->forCourse(
                        $department,
                        $course['name'],
                        "$codePrefix" . "E" . "$electiveCode",
                        $semesterValue,
                        'elective',
                        $course['difficulty']
                    )->create();
                    $electiveCode++;
                }
            }
        }
    }
}
