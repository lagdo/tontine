<?php

namespace Ajax\App\Report\Session\Saving;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class Amount extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.report.session.savings.amount', [
            'profitAmount' => $this->cache->get('report.profit'),
        ]);
    }
}
