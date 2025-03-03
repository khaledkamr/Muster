<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        $firstName = fake()->firstName($gender);
        $lastName = fake()->lastName('male');
        $email = fake()->unique()->safeEmail();
        $password = fake()->password();
        $phone = fake()->phoneNumber();

        return [
            'name' => "$firstName $lastName",
            'email' => $email,
            'password' => $password,
            'role' => fake()->randomElement(['student', 'professor', 'parent']), 
            'phone' => $phone,
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'gender' => $gender,
        ];
    }

    public function professor(): static
    {
        return $this->state(function (array $attributes) {
            $gender = $attributes['gender'];
            $firstName = fake()->firstName($gender);
            $lastName = fake()->lastName('male');
            return [
                'role' => 'professor',
                'name' => "Dr. $firstName $lastName",
                'birth_date' => fake()->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
                'department' => fake()->randomElement(['Computer Science', 'Mathematics', 'Physics', 'Information System', 'Artificial Intelligence']),
            ];
        });
    }

    public function student(): static
    {
        return $this->state(function (array $attributes) {
            $gender = $attributes['gender'];
            $firstName = fake()->firstName($gender);
            $lastName = fake()->lastName('male');
            return [
                'role' => 'student',
                'name' => "$firstName $lastName", 
                'birth_date' => fake()->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
                'major' => fake()->randomElement(['Computer Science', 'Information System', 'Artificial Intelligence']),
                'year' => fake()->randomElement(['freshman', 'sophomore', 'junior', 'senior']),
                'gpa' => fake()->randomFloat(2, 0, 4),
            ];
        });
    }

    public function parent(): static
    {
        return $this->state(function (array $attributes) {
            $gender = $attributes['gender'];
            $firstName = fake()->firstName($gender);
            $lastName = fake()->lastName('male');
            $prefix = $gender === 'male' ? 'Mr.' : 'Mrs.';
            return [
                'role' => 'parent',
                'name' => "$prefix $firstName $lastName",
                'birth_date' => fake()->dateTimeBetween('-60 years', '-40 years')->format('Y-m-d'),
            ];
        });
    }

    public function withParent(): static
    {
        return $this->afterCreating(function (User $user) {
            if ($user->role === 'student') {
                $parent = User::factory()->parent()->create();
                $user->update(['parent_id' => $parent->id]);
            }
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

   
}


