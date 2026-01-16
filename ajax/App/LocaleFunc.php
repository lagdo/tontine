<?php

namespace Ajax\App;

use Ajax\Base\FuncComponent;
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

    #[Callback('tontine.currency')]
    public function selectCurrency(string $countryCode): void
    {
        $html = $this->renderTpl('pages.admin.guild.currency', [
            'currencies' => !$countryCode ? [] :
                $this->localeService->getCountryCurrencies($countryCode),
        ]);
        $this->response->html('select_currency_container', $html);
    }
}
