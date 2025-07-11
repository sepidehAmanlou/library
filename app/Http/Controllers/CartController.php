<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class CartController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->auth_user;
    
        $pageSize = $request->input('page_size', 10);
        $details = Cart::where('user_id',$user->id)->with('book')->paginate($pageSize);
        $total =$details->sum(fn($item)=>$item->book->price*$item->quantity);

        return $this->output(200, ('errors.data_restored_successfully'),[
            'items'=>$details,
            'total_price'=>$total
        ]);
    }

    public function store(Request $request)
    {
        $user =$request->auth_user;

        $data = $request->only([ 'book_id', 'quantity']);
        $rules = [
            'book_id'  => 'required|integer|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ];
        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }
        $cart = Cart::where('user_id',$user->id)->where('book_id',$data['book_id'])->first();

        if($cart)
        {
            $cart->quantity += $data['quantity'];
            $cart->save();
        }
        else
        {
            $cart = Cart::create([
                'user_id' => $user->id,
                'book_id' => $data['book_id'],
                'quantity'=> $data['quantity']
            ]);
        }
        $cart->load(['book','user']);

        return $this->output(200,( 'errors.data_added_successfully'), $cart);
    }

    public function show(Request $request ,Cart $cart)
    {
        if($cart->user->id !== $request->auth_user->id && !$request->auth_user->isAdmin())
        {
            return $this->output(403,('errors.unauthorized'));
        }
       
        $cart->load(['user', 'book']);
        return $this->output(200, ('errors.data_restored_successfully'), $cart->toArray());
    }

    public function update(Request $request, Cart $cart)
    {
        if($cart->user->id !== $request->auth_user->id && !$request->auth_user->isAdmin())
        {
            return  $this->output(403,('errors.unauthorized'));
        }
        $data = $request->only(['quantity']);
        $rules = [
    
            'quantity' => 'required|integer|min:1',
        ];
        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $cart->update($data);
        $cart->load(['user', 'book']);

        return $this->output(200, ('errors.data_updated_successfully'), $cart);
    }

    public function destroy( Request $request ,Cart $cart)
    {
         if($cart->user->id !== $request->auth_user->id && !$request->auth_user->isAdmin())
        {
        return $this->output(403,('errors.unauthorized'));
        }
        $cart->delete();
        return $this->output(200, ('errors.data_deleted_successfully'));
    }
}
