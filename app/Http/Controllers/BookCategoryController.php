<?php

namespace App\Http\Controllers;

use App\Models\BookCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BookCategoryController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        $pageSize = $request->input('page_size',10);
        $details=BookCategory::with('language')->paginate($pageSize);
        return $this->output(200,('errors.data_restored_successfully'),$details);
    }

    public function store(Request $request)
    {
        $data = $request->only(['name','primary','language_id']);
        $rules =[
         'language_id'=>'required|integer|exists:languages,id',   
         'name'=>'required|string|min:3,max:255',
         'primary'=>'required|boolean'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

        if (isset($data['primary'])&& $data['primary']==true)
        {
            BookCategory::where('primary',true)->update(['primary'=>false]);
        }
        else{
            $exit = BookCategory::where('primary',true)->exists();
            if(!$exit)
            {
                $data['primary']=true;
            }
            else{
                $data['primary']=false;
            }
        }
        $bookCategory = BookCategory::create($data);
         return $this->output(200,('errors.data_added_successfully'),$bookCategory);
    }

    public function show(BookCategory $bookCategory)
    {
        $bookCategory->load('language');
        return $this->output(200,('errors.data_restored_successfully'),$bookCategory->toArray());
    }
    public function update(Request $request,BookCategory $bookCategory)
    {
        $data=$request->only(['name','primary','language_id']);
        $rules=[
            'language_id'=>'required|integer|exists:languages,id',
            'name'=>'required|string|min:3,max:255',
            'primary'=>'required|boolean'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

         if (isset($data['primary'])&& $data['primary']==true)
        {
            BookCategory::where('primary',true)->where('id','!=',$bookCategory->id)->update(['primary'=>false]);
        }
        else{
            $exit = BookCategory::where('primary',true)->where('id','!=',$bookCategory->id)->exists();
            if(!$exit)
            {
               $another = BookCategory::where('id','!=',$bookCategory->id)->first();
               if($another){
                $another->primary=true;
                $another->save();
               }
            }
           
        }
        $bookCategory->update($data);
        return $this->output(200,('errors.data_updated_successfully'),$bookCategory);
    }
    public function destroy(BookCategory $bookCategory)
    { 
        $isPrimary = $bookCategory->primary;
        $bookCategory->delete();
        if($isPrimary)
        {
            $another = BookCategory::first();
            if($another){
                $another->primary=true;
                $another->save();
            }
        }
        return $this->output(200,('errors.data_deleted_successfully'));
    }
}