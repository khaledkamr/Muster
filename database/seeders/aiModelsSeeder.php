<?php

namespace Database\Seeders;

use App\Models\aiModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class aiModelsSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            [
                'name' => 'LSTM',
                'description' => 'A Long Short-Term Memory (LSTM) model used to predict the student GPA and SGPA based on their performance in enrolled courses.',
            ],
            [
                'name' => 'K-Means Cluster',
                'description' => 'A K-Means clustering model used to cluster students for each course based on their performance, assisting professors in analysis.',
            ],
            [
                'name' => 'Content-Based Filter',
                'description' => 'A content-based filtering model for suggesting elective courses to students based on their performance in previous courses.',
            ],
            [
                'name' => 'Logistic Regression',
                'description' => 'A logistic regression model used to predict whether a student will pass or fail a course based on their performance in the course.',
            ],
        ];

        foreach($models as $model) {
            aiModel::create($model);
        }
    }
}
