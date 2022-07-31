<?php

namespace Siak\Tontine\Validation;

use Illuminate\Validation\Validator;
use Exception;

use function implode;
use function Jaxon\jaxon;
use function trans;

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

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $ajaxResponse = jaxon()->getResponse();
        $ajaxResponse->dialog->error($this->getMessage(), trans('common.titles.error'));

        return response($ajaxResponse->getOutput());
    }
}
