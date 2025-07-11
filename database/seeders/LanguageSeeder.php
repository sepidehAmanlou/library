<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            ['code' => 'en',     'title' => 'انگلیسی',      'primary' => false, 'code_flag' => 'gb'],
            ['code' => 'fa',     'title' => 'فارسی',        'primary' => true,  'code_flag' => 'ir'],
            ['code' => 'ar',     'title' => 'عربی',         'primary' => false, 'code_flag' => 'sa'],
            ['code' => 'fr',     'title' => 'فرانسوی',      'primary' => false, 'code_flag' => 'fr'],
            ['code' => 'es',     'title' => 'اسپانیایی',    'primary' => false, 'code_flag' => 'es'],
            ['code' => 'de',     'title' => 'آلمانی',       'primary' => false, 'code_flag' => 'de'],
            ['code' => 'ru',     'title' => 'روسی',         'primary' => false, 'code_flag' => 'ru'],
            ['code' => 'zh',     'title' => 'چینی',         'primary' => false, 'code_flag' => 'cn'],
            ['code' => 'tr',     'title' => 'ترکی',         'primary' => false, 'code_flag' => 'tr'],
            ['code' => 'hi',     'title' => 'هندی',         'primary' => false, 'code_flag' => 'in'],
            ['code' => 'ja',     'title' => 'ژاپنی',        'primary' => false, 'code_flag' => 'jp'],
            ['code' => 'ko',     'title' => 'کره‌ای',        'primary' => false, 'code_flag' => 'kr'],
            ['code' => 'it',     'title' => 'ایتالیایی',    'primary' => false, 'code_flag' => 'it'],
            ['code' => 'pt',     'title' => 'پرتغالی',      'primary' => false, 'code_flag' => 'pt'],
            ['code' => 'nl',     'title' => 'هلندی',        'primary' => false, 'code_flag' => 'nl'],
            ['code' => 'sv',     'title' => 'سوئدی',        'primary' => false, 'code_flag' => 'se'],
            ['code' => 'pl',     'title' => 'لهستانی',      'primary' => false, 'code_flag' => 'pl'],
            ['code' => 'uk',     'title' => 'اوکراینی',     'primary' => false, 'code_flag' => 'ua'],
            ['code' => 'he',     'title' => 'عبری',         'primary' => false, 'code_flag' => 'il'],
            ['code' => 'th',     'title' => 'تایلندی',      'primary' => false, 'code_flag' => 'th'],
            ['code' => 'cs',     'title' => 'چکی',          'primary' => false, 'code_flag' => 'cz'],
            ['code' => 'ro',     'title' => 'رومانیایی',    'primary' => false, 'code_flag' => 'ro'],
            ['code' => 'vi',     'title' => 'ویتنامی',      'primary' => false, 'code_flag' => 'vn'],
            ['code' => 'hu',     'title' => 'مجارستانی',    'primary' => false, 'code_flag' => 'hu'],
            ['code' => 'bn',     'title' => 'بنگالی',       'primary' => false, 'code_flag' => 'bd'],
            ['code' => 'id',     'title' => 'اندونزیایی',   'primary' => false, 'code_flag' => 'id'],
            ['code' => 'ms',     'title' => 'مالایی',        'primary' => false, 'code_flag' => 'my'],
            ['code' => 'fa-AF',  'title' => 'دری (افغانستان)', 'primary' => false, 'code_flag' => 'af'],
            ['code' => 'sr',     'title' => 'صربی',         'primary' => false, 'code_flag' => 'rs'],
            ['code' => 'fi',     'title' => 'فنلاندی',      'primary' => false, 'code_flag' => 'fi'],
        ];

        foreach ($languages as $lang) {
            Language::updateOrInsert(
                ['code' => $lang['code']],
                [
                    'title'     => $lang['title'],
                    'primary'   => $lang['primary'],
                    'code_flag' => $lang['code_flag'],
                    'status'    => 'active',
                ]
            );
        }
    }
}
