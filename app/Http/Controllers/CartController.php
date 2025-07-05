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
        $pageSize = $request->input('page_size', 10);
        $details = Cart::with(['user', 'book'])->paginate($pageSize);
        return $this->output(200, 'errors.data_restored_successfully', $details);
    }

    public function store(Request $request)
    {
        $data = $request->only(['user_id', 'book_id', 'quantity']);
        $rules = [
            'user_id'  => 'required|integer|exists:users,id',
            'book_id'  => 'required|integer|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ];
        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $cart = Cart::create($data);
        $cart->load(['user', 'book']);

        return $this->output(200, 'errors.data_added_successfully', $cart);
    }

    public function show(Cart $cart)
    {
        $cart->load(['user', 'book']);
        return $this->output(200, 'errors.data_restored_successfully', $cart->toArray());
    }

    public function update(Request $request, Cart $cart)
    {
        $data = $request->only(['user_id', 'book_id', 'quantity']);
        $rules = [
            'user_id'  => 'required|integer|exists:users,id',
            'book_id'  => 'required|integer|exists:books,id',
            'quantity' => 'required|integer|min:1',
        ];
        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $cart->update($data);
        $cart->load(['user', 'book']);

        return $this->output(200, 'errors.data_updated_successfully', $cart);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();
        return $this->output(200, 'errors.data_deleted_successfully');
    }
}
