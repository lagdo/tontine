<?php

namespace Ajax\App\Report\Session\Saving;

use Ajax\Component;

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
        return $this->renderView('pages.report.session.savings.amount', [
            'profitAmount' => $this->cache->get('report.profit'),
        ]);
    }
}
