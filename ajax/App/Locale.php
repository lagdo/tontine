<?php

namespace Ajax\App;

use Siak\Tontine\Service\LocaleService;
use Ajax\CallableClass;

class Locale extends CallableClass
{
    /**
     * @di
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @callback jaxon.ajax.callback.selectCurrency
     */
    public function selectCurrency(string $countryCode)
    {
        $html= $this->renderView('pages.tontine.currency', [
            'currencies' => $this->localeService->getCountryCurrencies($countryCode)
        ]);
        $this->response->html('select_currency_container', $html);
        return $this->response;
    }
}
