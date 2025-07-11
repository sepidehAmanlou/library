<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    
    public function definition()
    {
        // $faker = Factory::create('fa_IR');
        // return [
        //     'user_name' => $this->faker->unique()->userName(),
        //     'user_category_id' => $this->faker->numberBetween(2, 5), 
        //     'name' => $this->faker->name(),
        //     'email' => $this->faker->unique()->safeEmail(),
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'), 
        //     'gender' => $this->faker->randomElement(['male', 'female', 'other']),
        //     'status' => $this->faker->randomElement(['active', 'deactive']),
        //     'remember_token' => Str::random(10),
        // ];
    }
}
