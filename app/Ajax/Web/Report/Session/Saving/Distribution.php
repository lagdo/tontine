<?php

namespace App\Ajax\Web\Report\Session\Saving;

use App\Ajax\Component;

class Distribution extends Component
{
    /**
     * @inheritDoc
     */
    public function html(): string
    {
        [$savings] = $this->cl(Fund::class)->getData();

        return (string)$this->renderView('pages.report.session.savings.distribution', [
            'savings' => $savings->groupBy('member_id'),
            'distributionSum' => $savings->sum('distribution'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('report-fund-savings-distribution');
    }
}
