<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Rules\LanguageFlagMatch;
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

        $rules = [
            'title' => 'required|string|max:200',
            'code' => 'required|string|max:255',
            'code_flag' => [ 'required', 'string',new LanguageFlagMatch($data['code'] ?? ''),],
            'status' => 'required|in:active,deactive',
            'primary' => 'required|boolean'
        ];

        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $existing = Language::where('code', $data['code'])->first();
        if ($existing) {
            if ($existing->status === 'deactive') {
                $existing->update(array_merge($data, ['status' => 'active']));
            }else{
               $existing->update($data);
            } 

             if ($data['primary']) {
             Language::where('id', '!=', $existing->id)->where('primary', true)->update(['primary' => false]);
        }

        return $this->output(200, ('errors.data_added_successfully'), $existing);

        }

        if ($data['primary']) {
            Language::where('primary', true)->update(['primary' => false]);
        } else {

            $existingPrimary = Language::where('primary', true)->first();
            if (!$existingPrimary) {
                $data['primary'] = true;
            }
        }

        $language = Language::create($data);

        return $this->output(201, ('errors.data_added_successfully'), $language);
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
            'code_flag'=>['required', 'string', new LanguageFlagMatch($data['code'] ?? '')],
            'status'=>'required|in:active,deactive',
            'primary'=>'required|boolean'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

        if (!$data['primary'] && $language->primary) {
        $other = Language::where('id', '!=', $language->id)->first();
        if ($other) {
            $other->update(['primary' => true]);
        } else {
            $data['primary'] = true; 
        }
          }
        if($data['primary'])

        {
            Language::where('id','!=',$language->id)->where('primary',true)->update(['primary'=>false]);

        } else {
            $existingPrimary = Language::where('id','!=',$language->id)->where('primary', true)->first();
            if (!$existingPrimary) {
                $data['primary'] = true;
            }
        }
         
        $language->update($data);

        return $this->output(200,('errors.data_updated_successfully'),$language);
    }

    public function destroy(Language $language)
    {
       $wasPrimary = $language->primary;

        $language->update(['status' => 'deactive', 'primary' => false]);

        if ($wasPrimary) {
        
        $newPrimary = Language::where('status', 'active')->first();

        if ($newPrimary) {
            $newPrimary->update(['primary' => true]);
        }
    }

        return $this->output(200, ('errors.data_deleted_successfully'));
    }
}
