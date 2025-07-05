<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BookUserController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {   $user = $request->auth_user;
        $pageSize = $request->input('page_size',10);
        $myBook = $user->books()->paginate($pageSize);
        return $this->output(200,('errors.data_restored_successfully'),$myBook);
    }

    public function store(Request $request,Book $book)
    {
        $user = $request->auth_user;
        if($user->books()->where('book_id',$book)->exists())
        {
         return $this->output(401,('errors.book_already_added'));
        }
         $user->books()->attach($book->id,[
            'status'=>'unread',
            'added_at'=>now()
         ]);       

         return $this->output(200,('errors.book_added_successfully'));
    }

    public function update(Request $request,Book $book)
    {
        $user = $request->auth_user;

        $data = $request->only('status');
        $rules=[
            'status'=>'required|in:read,unread'
        ];
        $validatedData = $this->validation($data,$rules);
        if(!$validatedData->isSuccessful())
        return $validatedData;

        if(!$user->books()->where('book_id',$book->id)->exists())
        {
            return $this->output(404,('errors.book_not_found_in_your_list'));
        }

        $user->books()->updateExistingPivot($book->id,[
           'status'=> $data['status']
        ]);
        return $this->output(200,( 'errors.book_status_updated'));
    }

    public function destroy(Request $request, Book $book)
    {
        $user = $request->auth_user;
        if(!$user->books()->where('book_id',$book)->exists())
        {
            return $this->output(404,('errors.book_not_found_in_your_list'));
        }
        
        $user->books()->unattach($book->id);
        return $this->output(200,( 'errors.book_removed_from_your_list'));
    }
}
