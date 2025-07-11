<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;


class BookController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        $pageSize=$request->input('page_size',10);
        $details = Book::with('bookCategory.language')->paginate($pageSize); 
        return $this->output(200,('errors.data_restored_successfully'),$details);
    }

    public function store(Request $request)
    {
        $data =$request->only(['category_id','title','author','description']);
        $rules = [
            'category_id'=>'required|integer|exists:book_categories,id',
            'title'=>'required|string|min:3,max:255',
            'author'=>'required|string|min:2,max:255',
            'description'=>'sometimes|nullable|string'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;
       
        $book = Book::create($data);
        
        return $this->output(200,('errors.data_added_successfully'),$book);
    }

    public function show(Book $book)
    {
        $book->load('bookCategory.language');
        return $this->output(200,('errors.data_restored_successfully'),$book->toArray());
    }

    public function update(Request $request , Book $book)
    {
        $data=$request->only(['category_id','title','author','description']);
        $rules=[
            'category_id'=>'required|integer|exists:book_categories,id',
            'title'=>'required|string|min:3,max:255',
            'author'=>'required|string|min:2,max:255',
            'description'=>'sometimes|nullable|string'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;
       
        $book->update($data);
        return $this->output(200,('errors.data_updated_successfully'),$book);
    }

    public function destroy(Book $book)
    {   
        $book->delete();
        return $this->output(200,('errors.data_deleted_successfully'));
    }
}
