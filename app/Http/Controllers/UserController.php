<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Psy\CodeCleaner\PassableByReferencePass;

class UserController extends Controller
{
    use ApiResponse;
      public function index(Request $request)
      {
         $pageSize = $request->input('page_size',10);
         $details = User::with('userCategory')->paginate($pageSize);
         return $this->output(200,('errors.data_restored_successfully'),$details);
      }

      public function store(Request $request)
      {
        $data = $request->only([ 'user_name','name','email','password' ,
        'gender', 'user_category_id','password_confirmation'
        ]);

        $rules =[
            'user_name' => 'required|unique:users,user_name',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'gender' => 'required|in:male,female,other',
            'user_category_id' => 'required|exists:user_categories,id',
        ];

        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

        $data['password']=bcrypt($data['password']);
        
        
        $user = User::create($data);
         return $this->output(201,('errors.data_added_successfully'),$user);
        
      }

      public function show(User $user)
      {
        $user->load('userCategory');
        return $this->output(200,('errors.data_restored_successfully'),$user->toArray());
      }

      public function update(Request $request, User $user)
      {
        $data = $request->only([
            'user_name','name','email','password' ,
        'gender', 'user_category_id','password_confirmation'
        ]);

        $rules =[
            'user_name' => 'required|unique:users,user_name,'.$user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|nullable|min:6|confirmed',
            'gender' => 'required|in:male,female,other',
            'user_category_id' => 'required|exists:user_categories,id',
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

        if(!empty($data['password']))
        {
            $data['password'] = bcrypt($data['password']);
        }else{
            unset($data['password']);
        }
        
        $user->update($data);
        return $this->output(200,('errors.data_updated_successfully'),$user);
      }

      public function destroy(User $user)
      {
        $user->delete();
        return $this->output(200,('errors.data_deleted_successfully'));
      }
}
