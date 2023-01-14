<?php

namespace App\Ajax\App\Planning;

use App\Ajax\CallableClass;
use Carbon\Carbon;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Validation\Planning\SessionValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag session
 */
class Planning extends CallableClass
{
    /**
     * @di
     * @var SessionService
     */
    public SessionService $sessionService;

    /**
     * @var SessionValidator
     */
    protected SessionValidator $validator;

    public function home()
    {
        $html = $this->view()->render('tontine.pages.planning.session.home');
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());
        $this->jq('#btn-create')->click($this->rq()->number());

        return $this->page($this->bag('session')->get('page', 1));
    }
}
