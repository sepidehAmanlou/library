<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;


class OrderAdminController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $data = $request->only(['user_id', 'items', 'payment']);
        $rules = [
            'user_id' => 'required|integer|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|integer|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment.status' => 'required|in:pending,success,failed',
            'payment.gateway' => 'nullable|string|max:255',
            'payment.ref_number' => 'nullable|string|max:255',
            'payment.paid_at' => 'nullable|date',
        ];

        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $data['user_id'],
                'status' => $data['payment']['status'] === 'success' ? 'paid' : 'pending',
                'total_price' => 0,
                'payment_ref' => $data['payment']['ref_number'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            $order->updateTotalPrice();

            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'status' => $data['payment']['status'],
                'gateway' => $data['payment']['gateway'] ?? 'manual',
                'ref_number' => $data['payment']['ref_number'] ?? null,
                'paid_at' => $data['payment']['paid_at'] ?? now(),
            ]);

            DB::commit();

            $order->load(['user', 'orderItems.book', 'payments']);
            return $this->output(201, ('errors.data_added_successfully'), $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->output(500, ('errors.internal_error'), ['error' => $e->getMessage()]);
        }
    }

    }

