<?php

namespace App\Ajax\Web\Report\Session\Saving;

use App\Ajax\Component;

/**
 * @exclude
 */
class Distribution extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $savings = $this->cache->get('report.savings');

        return (string)$this->renderView('pages.report.session.savings.distribution', [
            'savings' => $savings->groupBy('member_id'),
            'distributionSum' => $savings->sum('distribution'),
        ]);
    }
}
