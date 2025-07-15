<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class OrderController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size', 10);
        $details = Order::with(['user', 'orderItems', 'payments'])->paginate($pageSize);
        return $this->output(200, ('errors.data_restored_successfully'), $details);
    }

    public function store(Request $request)
    {
        $data = $request->only(['user_id', 'total_price', 'status', 'payment_ref']);
        $rules = [
            'user_id'      => 'required|integer|exists:users,id',
            'total_price'  => 'required|numeric|min:0',
            'status'       => 'required|in:pending,paid,canceled',
            'payment_ref'  => 'sometimes|nullable|string|max:255'
        ];

        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $order = Order::create($data);
        $order->load(['user', 'orderItems', 'payments']);

        return $this->output(201, ('errors.data_added_successfully'), $order);
    }

    public function show(Request $request ,Order $order)
    {
        if ($order->user_id !== $request->auth_user->id && !$request->auth_user->isAdmin()) {
        return $this->output(403, ('errors.unauthorized'));
    }
        $order->load(['user', 'orderItems', 'payments']);
        return $this->output(200, ('errors.data_restored_successfully'), $order->toArray());
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->only([ 'status', 'payment_ref']);
        $rules = [
            'status'       => 'required|in:pending,paid,canceled',
            'payment_ref'  => 'sometimes|nullable|string|max:255'
        ];

        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $order->update($data);
        $order->load(['user', 'orderItems', 'payments']);

        return $this->output(200, ('errors.data_updated_successfully'), $order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return $this->output(200, ('errors.data_deleted_successfully'));
    }
}
