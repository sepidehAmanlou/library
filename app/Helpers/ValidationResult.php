<?php

namespace App\Helpers;

class ValidationResult
{
   
    public bool $success;
    public $errors;
    public $data;
    public function __construct(bool $success, $errors = null, $data = null)
    {
        $this->success = $success;
        $this->errors = $errors;
        $this->data = $data;
    }
}
