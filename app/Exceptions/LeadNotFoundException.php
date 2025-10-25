<?php

namespace App\Exceptions;

use Exception;

class LeadNotFoundException extends Exception
{
    protected $source;

    public function __construct($message = 'Lead not found', $code = 404, $source = 'LeadController')
    {
        parent::__construct($message, $code);
        $this->source = $source;
    }

    public function render()
    {
        return response()->json((new ErrorResponse($this->getMessage(), $this->getCode(), $this->source))->toArray(), $this->getCode());
    }
}
