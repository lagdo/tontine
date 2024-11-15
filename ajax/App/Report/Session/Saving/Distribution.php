<?php

namespace Ajax\App\Report\Session\Saving;

use Ajax\Component;

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

        return $this->renderView('pages.report.session.savings.distribution', [
            'savings' => $savings->groupBy('member_id'),
            'distributionSum' => $savings->sum('distribution'),
        ]);
    }
}
