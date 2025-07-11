<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
       
        DB::table('users')->insert([
            'id' => 1,
            'user_name' => 'admin',
            'user_category_id' => 1, 
            'name' => 'مدیر سیستم',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'), 
            'gender' => 'other',
            'status' => 'active',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

      
    //    User::factory()->count(9)->create();

 $faker = Faker::create('fa_IR');

        foreach (range(2, 10) as $id) {
            User::create([
                'user_name' => $faker->unique()->userName,
                'user_category_id' => $faker->numberBetween(2, 5), // فرض بر اینکه دسته‌های کاربری دیگه از 2 به بعد هستن
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'status' => $faker->randomElement(['active', 'deactive']),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }

        DB::statement("ALTER TABLE users AUTO_INCREMENT = 11");
    }
}
