<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class PaymentController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size', 10);
        $details = Payment::with('order')->paginate($pageSize);
        return $this->output(200, 'errors.data_restored_successfully', $details);
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

        return $this->output(200, 'errors.data_added_successfully', $payment);
    }

    public function show(Payment $payment)
    {
        $payment->load('order');
        return $this->output(200, 'errors.data_restored_successfully', $payment->toArray());
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

        return $this->output(200, 'errors.data_updated_successfully', $payment);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return $this->output(200, 'errors.data_deleted_successfully');
    }
}

