<?php

namespace App\Http\Controllers;

use App\Models\UserCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserCategoryController extends Controller
{   
    use ApiResponse;
    public function index(Request $request)
    {
        $pageSize = $request->input('page_size',10);
        $details = UserCategory::paginate($pageSize);
        return $this->output(200,('errors.data_restored_successfully'),$details);
    }

    public function store(Request $request)
    {
        $data = $request->only(['name','description']);
        $rules = [
        'name' => 'required|unique:user_categories,name|max:255',
        'description' => 'sometimes|nullable|string',
        ];
        $validatedData = $this->validation($data,$rules);
        if (!$validatedData->isSuccessful())
        return $validatedData;

        $userCategory = UserCategory::create($data);
        return $this->output(201,('errors.data_added_successfully'),$userCategory);
    }

    public function show(UserCategory $userCategory)
    {
        return $this->output(200,('errors.data_restored_successfully'),$userCategory->toArray());
    }

    public function update(Request $request,UserCategory $userCategory)
    {
        $data = $request->only(['name','description']);
        $rules =[
        'name' => 'required|unique:user_categories,name|max:255',
        'description' => 'sometimes|nullable|string',
        ];
        $validatedData = $this->validation($data,$rules);
        if (!$validatedData->isSuccessful())
        return $validatedData;

        $userCategory->update($data);
        return $this->output(200,('errors.data_updated_successfully'),$userCategory);
    }
    public function destroy(UserCategory $userCategory)
    {
        $userCategory->delete();
        return $this->output(200,('errors.data_deleted_successfully'));
    }
}
