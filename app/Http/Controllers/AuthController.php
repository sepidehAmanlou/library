<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;
    public function register(Request $request)
    {
        $data = $request->only(['user_name','name','email','password' ,
        'gender', 'user_category_id','password_confirmation']);

        $rules =[
            'user_name' => 'required|unique:users,user_name',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'gender' => 'required|in:male,female,other',
            'user_category_id' => 'nullable|exists:user_categories,id',
        ];
        
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;
         
        $data['password']=bcrypt($data['password']);
        
        $user =User::create($data);
        return $this->output(200,('errors.register_successful'),$user);
    }


    public function login(Request $request)
    {
        $data = $request->only(['email','password']);
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;
        
        $user = User::where('email',$data['email'])->first();
        if(!$user || !Hash::check($data['password'],$user->password))
        {
           return $this->output(401, 'errors.invalid_credentials');

        }
        
        $token = bin2hex(random_bytes(32));
        $expiresAt = now()->addHour();

        $user->userTokens()->create([
            'token'=> $token,
            'device'=>$request->userAgent(),
            'expires_at'=>$expiresAt
        ]);

        return $this->output(200,( 'errors.login_successful'), [
           'token' => $token,
           'expires_at' => $expiresAt
        ]);

    }
}
