<?php

namespace App\Ajax\App;

use Siak\Tontine\Service\LocaleService;
use App\Ajax\CallableClass;

class Locale extends CallableClass
{
    /**
     * @di
     * @var LocaleService
     */
    protected LocaleService $localeService;

    public function selectCurrencies(string $countryCode)
    {
        $html= $this->view()->render('tontine.pages.tontine.currency', [
            'currencies' => $this->localeService->getCountryCurrencies($countryCode)
        ]);
        $this->response->html('select_currency_container', $html);
        return $this->response;
    }
}
