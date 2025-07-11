<?php

namespace Database\Seeders;

use App\Models\UserCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'ادمین',      'description' => 'مدیر سیستم'],
            ['id' => 2, 'name' => 'مهمان',      'description' => 'کاربر مهمان'],
            ['id' => 3, 'name' => 'کاربر عادی', 'description' => 'کاربر ثبت‌نام شده'],
            ['id' => 4, 'name' => 'دانشجو',     'description' => 'کاربر دانشجو'],
            ['id' => 5, 'name' => 'حسابدار',    'description' => 'کاربر مالی'],
            ['id' => 6, 'name' => 'مدرس',       'description' => 'کاربر آموزشی'],
        ];

        foreach ($categories as $category) {
            UserCategory::updateOrInsert(
                ['id' => $category['id']],
                ['name' => $category['name'], 'description' => $category['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        DB::statement("ALTER TABLE user_categories AUTO_INCREMENT = 7");
    }
}
