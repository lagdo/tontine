<?php

namespace Ajax\App;

use Ajax\FuncComponent;
use Jaxon\Attributes\Attribute\Callback;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\LocaleService;

class LocaleFunc extends FuncComponent
{
    /**
     * @var LocaleService
     */
    #[Inject]
    protected LocaleService $localeService;

    #[Callback('jaxon.ajax.callback.selectCurrency')]
    public function selectCurrency(string $countryCode): void
    {
        $html = $this->renderView('pages.admin.guild.currency', [
            'currencies' => !$countryCode ? [] :
                $this->localeService->getCountryCurrencies($countryCode),
        ]);
        $this->response->html('select_currency_container', $html);
    }
}
