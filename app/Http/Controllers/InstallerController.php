<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InstallerController extends Controller
{
    use ApiResponse;
    public function checkINstallation()
    {
        $installed = User::where('user_category_id',1)->exists();
        if($installed){
            return $this->output(200,('errors.system_installed'));
        }
        return $this->output(200,('errors.system_not_installed'));
        
    }

    public function install(Request $request)
    {
        $installed = User::where('user_category_id',1)->exists();
        if($installed){
            return $this->output(400,('errors.system_already_installed'));
        }
        $data = $request->only(['email','password']);
        $rules = [
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

        $admin = User::crate([
             'email'=> $data['email'],
             'password'=>$data['password'] ,
             'email_verified_at' => now(),
        ]);
        
        return $this->output(201, 'messages.system_installed_successfully', $admin);
    }
}
