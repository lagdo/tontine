<?php

namespace App\Ajax\Web\Report\Session\Saving;

use App\Ajax\Component;

/**
 * @exclude
 */
class Amount extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.report.session.savings.amount', [
            'profitAmount' => $this->cache->get('report.profit'),
        ]);
    }
}
