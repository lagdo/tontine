<?php

namespace App\Ajax\Web\Report\Session\Saving;

use App\Ajax\Component;

class Amount extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        [, , , $profitAmount] = $this->cl(Fund::class)->getData();

        return (string)$this->renderView('pages.report.session.savings.amount', [
            'profitAmount' => $profitAmount,
        ]);
    }
}
