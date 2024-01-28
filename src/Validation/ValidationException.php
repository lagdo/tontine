<?php

namespace Siak\Tontine\Validation;

use Illuminate\Validation\Validator;
use Exception;

use function implode;

class ValidationException extends Exception
{
    /**
     * The constructor
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        parent::__construct(implode('<br/>', $validator->errors()->all()));
    }
}
