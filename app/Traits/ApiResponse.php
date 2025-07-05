<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Support\ValidationResult;

trait ApiResponse
{
    public function validation(array $data, array $rules, array $messages = []): ValidationResult
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return new ValidationResult(false, $validator->errors());
        }

        return new ValidationResult(true, null, $data);
    }

    public function output(int $statusCode, string $message, $data = null): JsonResponse
    {
        $response = [
            'status' => $statusCode,
            'message' => __($message),
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public function input(Request $request, array $only = [], array $except = []): array
    {
        if (!empty($only)) {
            return $request->only($only);
        }

        if (!empty($except)) {
            return $request->except($except);
        }

        return $request->all();
    }
}
