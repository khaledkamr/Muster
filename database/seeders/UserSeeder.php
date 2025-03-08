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
        $users = [
            [
                'name' => 'Khaled Kamr',
                'email' => 'kk@gmail.com',
                'password' => '111',
                'role' => 'student',
                'gender' => 'male',
                'year' => '2021',
                'gpa' => 3.5,
                'major' => 'Computer Science',
            ],
            [
                'name' => 'Ahmed Khaled',
                'email' => 'ak@gmail.com',
                'password' => '111',
                'role' => 'student',
                'gender' => 'male',
                'year' => '2021',
                'gpa' => 3.5,
                'major' => 'Computer Science'
            ],
            [
                'name' => 'Kamr Rashad',
                'email' => 'kr@gmail.com',
                'password' => '111',
                'role' => 'parent',
                'gender' => 'male'
            ],
            [
                'name' => 'Dr. Alaa El-Deen',
                'email' => 'ae@gmail.com',
                'password' => '111',
                'role' => 'professor',
                'gender' => 'male'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}