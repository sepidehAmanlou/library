<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class OrderItemController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size', 10);
        $details = OrderItem::with(['order', 'book'])->paginate($pageSize);
        return $this->output(200, ('errors.data_restored_successfully'), $details);
    }

    public function store(Request $request)
    {
        $data = $request->only(['order_id', 'book_id', 'quantity', 'price']);
        $rules = [
            'order_id'  => 'required|integer|exists:orders,id',
            'book_id'   => 'required|integer|exists:books,id',
            'quantity'  => 'required|integer|min:1',
            'price'     => 'required|numeric|min:0',
        ];

        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $order = Order::find($data['order_id']);
        if (!$order || ($order->user_id !== $request->auth_user->id && !$request->auth_user->isAdmin())) {
            return $this->output(403, ('errors.unauthorized'));
        }  

        $orderItem = OrderItem::create($data);
        $orderItem->load(['order', 'book']);

        return $this->output(200, ('errors.data_added_successfully'), $orderItem);
    }

    public function show( Request $request,OrderItem $orderItem)
    {
        if ($orderItem->order->user_id !== $request->auth_user->id && !$request->auth_user->isAdmin()) {
            return $this->output(403, ('errors.unauthorized'));
        }
        $orderItem->load(['order', 'book']);
        return $this->output(200, ('errors.data_restored_successfully'), $orderItem->toArray());
    }

    public function update(Request $request, OrderItem $orderItem)
    {
        $data = $request->only(['order_id', 'book_id', 'quantity', 'price']);
        $rules = [
            'order_id'  => 'required|integer|exists:orders,id',
            'book_id'   => 'required|integer|exists:books,id',
            'quantity'  => 'required|integer|min:1',
            'price'     => 'required|numeric|min:0',
        ];

        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $orderItem->update($data);
        $orderItem->load(['order', 'book']);

        return $this->output(200, ('errors.data_updated_successfully'), $orderItem);
    }

    public function destroy(OrderItem $orderItem)
    {
        $orderItem->delete();
        return $this->output(200,( 'errors.data_deleted_successfully'));
    }
}

