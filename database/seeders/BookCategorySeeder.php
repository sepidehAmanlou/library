<?php

namespace Database\Seeders;

use App\Models\BookCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BookCategorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fa_IR');
        $categories = [
            ['id' => 1, 'name' => 'داستان',          'primary' => true],
            ['id' => 2, 'name' => 'علمی',            'primary' => false],
            ['id' => 3, 'name' => 'فلسفی',           'primary' => false],
            ['id' => 4, 'name' => 'کودک و نوجوان',   'primary' => false],
            ['id' => 5, 'name' => 'تاریخی',          'primary' => false],
        ];

        foreach ($categories as $category) {
           BookCategory::updateOrInsert(
                ['id' => $category['id']],
                [
                    'name'       => $category['name'],
                    'primary'    => $category['primary'],
                    'language_id'=>$faker->numberBetween(1,30),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        DB::statement("ALTER TABLE book_categories AUTO_INCREMENT = 6");
    }
}
