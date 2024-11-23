<?php

namespace Ajax\App\Report\Session\Saving;

use Ajax\Component;
use Stringable;

/**
 * @exclude
 */
class Distribution extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $savings = $this->cache->get('report.savings');

        return $this->renderView('pages.report.session.savings.distribution', [
            'savings' => $savings->groupBy('member_id'),
            'distributionSum' => $savings->sum('distribution'),
        ]);
    }
}
