<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        $pageSize = $request->input('page_size',10);
        $details = Language::paginate($pageSize);
        return $this->output(200,('errors.data_restored_successfully'),$details);
    } 

    public function store(Request $request)
    {
        $data = $request->only(['title','code','code_flag','status','primary']);
        $rules =[
            'title'=>'required|string|max:200',
            'code'=>'required|string|max:255',
            'code_flag'=>'required|string|max:255',
            'status'=>'required|in:active,deactive',
            'primary'=>'required|boolean'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

        $existing = Language::where('code', $data['code'])->first();

        if ($existing && $existing->status === 'deactive') {
            $existing->update(array_merge($data, ['status' => 'active']));
            return $existing;
        }

        if ($existing) {
            return $existing;
        }

        
        if($data['primary'])
        {
            Language::where('primary',true)->update(['peimary'=>false]);
        }else
        {
            $existingPrimary = Language::where('primary',true)->first();
            if(!$existingPrimary)
            {
                $data ['primary'] = true;
            }
            $language = Language::create($data);

            return $this->output(200,('errors.data_added_successfully'),$language);
        }
        
       
    }
    public function show(Language $language)
    {
        return $this->output(200,('errors.data_restored_successfully'),$language->toArray());
    }

    public function update(Request $request,Language $language)
    {  
         $data = $request->only(['title','code','code_flag','status','primary']);
        $rules =[
            'title'=>'required|string|max:200',
            'code'=>'required|string|max:255',
            'code_flag'=>'required|string|max:255',
            'status'=>'required|in:active,deactive',
            'primary'=>'required|boolean'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

        if($data['primary'])

        {
            Language::where('primary',true)->update(['primary'=>false]);
        }else{
            if($language->primary)
            {
               $otherLnguage = Language::where('id','!=',$language->id)->first();
            
            if($otherLnguage)
            {
                $otherLnguage->update(['primary'=>true]);
            }else{
                $data['primary']= true ; 
            }
        }else {
            $existingPrimary = Language::where('primary', true)->first();
            if (!$existingPrimary) {
                $data['primary'] = true;
            }
        }
         
      $language->update($data);
       return $this->output(200,('errors.data_updated_successfully'),$language);
        }
        
    }

    public function destroy(Language $language)
    {
       $language->update(['status' => 'deactive']);

    return $this->output(200, __('errors.data_deleted_successfully'));
    }
}
