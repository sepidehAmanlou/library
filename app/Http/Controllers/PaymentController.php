<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size', 10);
        $details = Payment::with('order')->paginate($pageSize);
        return $this->output(200, ('errors.data_restored_successfully'), $details);
    }

    public function store(Request $request)
    {
        $data = $request->only(['order_id', 'amount', 'status', 'gateway', 'ref_number', 'paid_at']);
        $rules = [
            'order_id'   => 'required|integer|exists:orders,id',
            'amount'     => 'required|numeric|min:0',
            'status'     => 'required|in:success,failed,pending',
            'gateway'    => 'sometimes|nullable|string|max:255',
            'ref_number' => 'sometimes|nullable|string|max:255',
            'paid_at'    => 'sometimes|nullable|date',
        ];

        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $payment = Payment::create($data);
        $payment->load('order');

        return $this->output(200, ('errors.data_added_successfully'), $payment);
    }

    public function show(Payment $payment)
    {
        $payment->load('order');
        return $this->output(200, ('errors.data_restored_successfully'), $payment->toArray());
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->only(['order_id', 'amount', 'status', 'gateway', 'ref_number', 'paid_at']);
        $rules = [
            'order_id'   => 'required|integer|exists:orders,id',
            'amount'     => 'required|numeric|min:0',
            'status'     => 'required|in:success,failed,pending',
            'gateway'    => 'sometimes|nullable|string|max:255',
            'ref_number' => 'sometimes|nullable|string|max:255',
            'paid_at'    => 'sometimes|nullable|date',
        ];

        $validatedData = $this->validation($data, $rules);
        if (!$validatedData->isSuccessful()) {
            return $validatedData;
        }

        $payment->update($data);
        $payment->load('order');

        return $this->output(200, ('errors.data_updated_successfully'), $payment);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return $this->output(200,( 'errors.data_deleted_successfully'));
    }


 public function pay(Request $request)
{
    $data = $request->only('order_id');
    $rules = [
        'order_id' => 'required|integer|exists:orders,id',
    ];
    $validatedData = $this->validation($data, $rules);

    if (!$validatedData->isSuccessful())
        return $validatedData;

    $order = Order::find($data['order_id']);

    if ($order->user_id !== $request->auth_user->id && !$request->auth_user->isAdmin()) {
        return $this->output(403, ('errors.unauthorized'));
    }

    $amount = $order->total_price * 10;
    $callbackUrl = route('payments.callback', ['order_id' => $order->id]);

    $zarinData = [
        'MerchantID'  => env('ZARINPAL_MERCHANT_ID'),
        'Amount'      => $amount,
        'Description' => "پرداخت سفارش شماره {$order->id}",
        'CallbackURL' => $callbackUrl,
    ];

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->post('https://api.zarinpal.com/pg/v4/payment/request.json', $zarinData);

    $result = $response->json();

    if (isset($result['data']['authority']) && $result['data']['code'] == 100) {
        DB::transaction(function () use ($order) {
            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'status' => 'pending',
                'gateway' => 'zarinpal',
            ]);
        });

        return $this->output(200, ('errors.payment_redirect_url'), [
            'pay_url' => "https://www.zarinpal.com/pg/StartPay/{$result['data']['authority']}"
        ]);
    }

    return $this->output(500, ('errors.payment_request_failed'), $result);
}

public function callback(Request $request)
{
    $orderId = $request->input('order_id');
    $authority = $request->input('Authority');
    $status = $request->input('Status');

    $order = Order::find($orderId);
    if (!$order) {
        return $this->output(404, 'errors.order_not_found');
    }

    $merchantId = env('ZARINPAL_MERCHANT_ID');

    if ($status === 'OK') {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api.zarinpal.com/pg/v4/payment/verify.json', [
            'MerchantID' => $merchantId,
            'Authority'  => $authority,
            'Amount'     => $order->total_price * 10,
        ]);

        $result = $response->json();

        if (isset($result['data']['code']) && $result['data']['code'] == 100) {
            $payment = Payment::where('order_id', $order->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($payment) {
                DB::transaction(function () use ($payment, $order, $authority) {
                    $payment->update([
                        'status' => 'success',
                        'ref_number' => $authority,
                        'paid_at' => now(),
                    ]);
                    $order->update(['status' => 'paid']);
                });
            }

            return $this->output(200, ('errors.payment_success'));
        } else {
            $payment = Payment::where('order_id', $order->id)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($payment) {
                $payment->update(['status' => 'failed']);
            }

            return $this->output(402, ('errors.payment_failed'));
        }
    } else {
        return $this->output(402, ('errors.payment_failed'));
    }
}
 
}

