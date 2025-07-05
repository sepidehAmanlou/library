<?php

namespace App\Support;

use Illuminate\Contracts\Support\Responsable;

class ValidationResult implements Responsable
{
    protected bool $success;
    protected mixed $errors;
    protected array $data;

    public function __construct(bool $success, $errors = null, array $data = [])
    {
        $this->success = $success;
        $this->errors = $errors;
        $this->data = $data;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function toResponse($request)
    {
        if ($this->isSuccessful()) {
            return response()->json([
                'status' => 200,
                'message' => 'اعتبارسنجی داده‌هاموفق بود.',
                'data' => $this->data(),
            ]);
        }

        return response()->json([
            'status' => 422,
            'message' => 'اعتبارسنجی داده‌ها ناموفق بود.',
            'errors' => $this->errors(),
        ], 422);
    }
}
