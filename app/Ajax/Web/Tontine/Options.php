<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\CallableClass;

use function trans;

class Options extends CallableClass
{
    public function home()
    {
        $this->response->html('section-title', trans('tontine.menus.tontine'));
        $this->response->html('content-home', $this->view()->render('tontine.pages.tontine.options'));

        $this->cl(Fund::class)->home();
        $this->cl(Category::class)->home();
        $this->cl(Charge::class)->home();
    }
}
