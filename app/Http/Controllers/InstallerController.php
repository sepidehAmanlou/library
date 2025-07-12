<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use App\Models\UserCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\DB;

class InstallerController extends Controller
{
    use ApiResponse;
    public function checkINstallation()
    { 
        $adminExists = User::where('user_category_id', 1)->exists();
        $primaryLanguageExists = Language::where('primary', true)->exists();

    if ($adminExists && $primaryLanguageExists) {
        return $this->output(200, __('errors.system_installed'));
    }
        return $this->output(200,('errors.system_not_installed'));
        
    }

    public function install(Request $request)
    {
       $installed = User::where('user_category_id',1)->exists();
       $languageInstalled = Language::where('primary', true)->exists();

       if ($installed && $languageInstalled) {

        return $this->output(400,('errors.system_already_installed'));
        }
        $data = $request->only(['email','password','title', 'code','user_category_id','user_name','name','gender','status']);
        $rules = [
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'title' => 'required|string|max:200',
        'code' => 'required|string|max:10',
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;
       
        UserCategory::updateOrCreate(
        ['id' => 1],
        ['name' => ' admin', 'status' => 'active']
        );

    try {
        $result = DB::transaction(function () use ($data) {
            $language = Language::create([
                'title' => $data['title'],
                'code' => $data['code'],
                'primary' => true,
                'code_flag' => 'flag-' . $data['code'],
                'status' => 'active',
            ]);

            $admin = User::create([
                 'email' => $data['email'],
                 'password' => bcrypt($data['password']),
                 'user_category_id' => 1,
                 'email_verified_at' => now(),
                 'user_name' => 'admin_' . rand(1000,9999),
                 'name' => 'مدیر سیستم',
                 'gender' => 'other',
                 'status' => 'active',
            ]);   

            return $admin;
        });

        return $this->output(201, ('errors.system_installed_successfully'), $result);

    } catch (Throwable $e) {
        
        return $this->output(500, ('errors.system_installation_failed'));
        
    }
  }
}
