<?php

namespace App\Http\Controllers;

use Jaxon\Laravel\Jaxon;

class JaxonController extends Controller
{
    public function jaxon(Jaxon $jaxon)
    {
        $jaxon->callback()->init(function($callable) use($jaxon) {
            // Jaxon init
            $callable->dialog = $jaxon->ajaxResponse()->dialog;
            $callable->notify = $jaxon->ajaxResponse()->dialog;
        });

        // Process Ajax request
        $jaxon->processRequest();
        return $jaxon->httpResponse();
    }
}
