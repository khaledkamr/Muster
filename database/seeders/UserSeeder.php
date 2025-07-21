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
            // [
            //     'name' => 'Kamr Rashad',
            //     'email' => 'kr@gmail.com',
            //     'password' => '111',
            //     'role' => 'parent',
            //     'gender' => 'male',
            //     'phone' => '0593230125',
            //     'birth_date' => '1973-05-03'
            // ],
            // [
            //     'name' => 'Khaled Kamr',
            //     'email' => 'kk@gmail.com',
            //     'password' => '111',
            //     'role' => 'student',
            //     'gender' => 'male',
            //     'year' => 'senior',
            //     'major' => 'Computer Science',
            //     'phone' => '01006379503',
            //     'birth_date' => '2003-07-03',
            //     'parent_id' => '1'
            // ],
            // [
            //     'name' => 'Yossif Kamr',
            //     'email' => 'yk@gmail.com',
            //     'password' => '111',
            //     'role' => 'student',
            //     'gender' => 'male',
            //     'year' => 'junior',
            //     'major' => 'Computer Science',
            //     'phone' => '01006379503',
            //     'birth_date' => '2005-07-03',
            //     'parent_id' => '1'
            // ],
            // [
            //     'name' => 'Dr. Mohamed Marey',
            //     'email' => 'mm@gmail.com',
            //     'password' => '111',
            //     'role' => 'professor',
            //     'gender' => 'male',
            //     'department' => 'Artificial Intelligence',
            //     'phone' => '0123456789',
            //     'birth_date' => '1980-01-01'
            // ],
            // [
            //     'name' => 'Ahmed mohammed',
            //     'email' => 'am@gmail.com',
            //     'password' => '111',
            //     'role' => 'admin',
            //     'gender' => 'male',
            //     'phone' => '0123456789',
            //     'birth_date' => '1981-01-01'
            // ],
            [
                'name' => 'Adam Ashraf',
                'email' => 'aa@gmail.com',
                'password' => '111',
                'role' => 'student',
                'gender' => 'male',
                'phone' => '0123456789',
                'birth_date' => '1981-01-01'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}