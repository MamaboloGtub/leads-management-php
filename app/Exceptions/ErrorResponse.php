<?php

namespace App\Exceptions;

class ErrorResponse
{
    public $message;
    public $code;
    public $source;

    public function __construct($message, $code = 400, $source = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->source = $source;
    }

    public function toArray()
    {
        return [
            'message' => $this->message,
            'code' => $this->code,
            'source' => $this->source,
        ];
    }
}
