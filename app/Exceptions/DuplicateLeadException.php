<?php

namespace App\Exceptions;

use Exception;

class DuplicateLeadException extends Exception
{
    protected $source;

    public function __construct($message = 'Duplicate lead', $code = 409, $source = 'LeadController')
    {
        parent::__construct($message, $code);
        $this->source = $source;
    }

    public function render()
    {
        return response()->json((new ErrorResponse($this->getMessage(), $this->getCode(), $this->source))->toArray(), $this->getCode());
    }
}
