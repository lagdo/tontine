<?php

namespace Ajax\App;

use Siak\Tontine\Service\LocaleService;
use Ajax\FuncComponent;

class LocaleFunc extends FuncComponent
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
        $html = $this->renderView('pages.admin.guild.currency', [
            'currencies' => !$countryCode ? [] :
                $this->localeService->getCountryCurrencies($countryCode),
        ]);
        $this->response->html('select_currency_container', $html);
    }
}
