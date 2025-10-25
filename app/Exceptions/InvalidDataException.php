<?php

namespace App\Exceptions;

use Exception;

class InvalidDataException extends Exception
{
    protected $source;

    public function __construct($message = 'Invalid data', $code = 422, $source = 'LeadController')
    {
        parent::__construct($message, $code);
        $this->source = $source;
    }

    public function render()
    {
        return response()->json((new ErrorResponse($this->getMessage(), $this->getCode(), $this->source))->toArray(), $this->getCode());
    }
}
