<?php

namespace App\Http\Controllers;

use App\Ajax\CallableClass;
use Jaxon\Laravel\Jaxon;

class JaxonController extends Controller
{
    public function jaxon(Jaxon $jaxon)
    {
        $jaxon->callback()->init(function(CallableClass $callable) use($jaxon) {
            // Jaxon init
            $dialog = $jaxon->ajaxResponse()->dialog;
            $callable->dialog = $dialog;
            $callable->notify = $dialog;
        });

        // Process Ajax request
        $jaxon->processRequest();
        return $jaxon->httpResponse();
    }
}
