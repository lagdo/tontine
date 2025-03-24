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
        return $this->renderView('pages.report.session.savings.distribution', [
            'distribution' => $this->stash()->get('report.savings.distribution'),
        ]);
    }
}
