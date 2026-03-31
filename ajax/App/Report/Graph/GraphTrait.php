<?php

namespace Ajax\App\Report\Graph;

use Jaxon\Flot\FlotPlugin;
use Jaxon\Flot\Chart\Card;
use Jaxon\Response\AjaxResponse;

trait GraphTrait
{
    /**
     * @var string
     */
    protected string $graphId = '';

    /**
     * @var string
     */
    protected string $graphHeight = '300px';

    /**
     * @return AjaxResponse
     */
    abstract protected function response(): AjaxResponse;

    /**
     * @return FlotPlugin
     */
    private function flot(): FlotPlugin
    {
        return $this->response()->plugin(FlotPlugin::class);
    }

    /**
     * @return Card
     */
    private function card(): Card
    {
        // The width will be automatically set by the Flot plugin.
        return $this->flot()->card($this->graphId)->height($this->graphHeight);
    }

    /**
     * @return array
     */
    private function barOptions(): array
    {
        return [
            'series' => [
                'labels' => [
                    'show' => false,
                    // 'position' => 'middle',
                    // 'angle' => 90,
                    // 'labelFormatter' => 'tontine.flot.formatLabel',
                ],
            ],
            'yaxis' => [
                'show' => false,
            ],
            'legend' => [
                'show' => true,
                'noColumns' => 2,
                'container' => "{$this->graphId}-labels",
                'labelFormatter' => 'tontine.flot.formatLabel',
            ],
        ];
    }

    /**
     * @return array
     */
    private function pieOptions(): array
    {
        return [
            'series' => [
                'pie' => [
                    'show' => true,
                    'radius' => 0.8,
                    'innerRadius' => 0.5,
                    'label' => [
                        'show' => true,
                        'radius' => 1,
                    ],
                ],
            ],
            'legend' => [
                'show' => false,
            ],
        ];
    }

    /**
     * @return array
     */
    private function lineOptions(): array
    {
        return [
            'legend' => [
                'show' => true,
                'noColumns' => 3,
                'container' => "{$this->graphId}-labels",
            ],
            'yaxis' => [
                'show' => false,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return "<div id=\"{$this->graphId}\"></div>";
    }

    /**
     * @return array<string, int>
     */
    private function counters(): array
    {
        return [
            'deposits' => ['order' => 7, 'coef' => 1],
            'remitments' => ['order' => 6, 'coef' => -1],
            'settlements' => ['order' => 5, 'coef' => 1],
            'outflows' => ['order' => 4, 'coef' => -1],
            'savings' => ['order' => 3, 'coef' => 1],
            'loans' => ['order' => 2, 'coef' => -1],
            'refunds' => ['order' => 1, 'coef' => 1],
        ];
    }

    /**
     * @param string $counter
     * @param int $sessionId
     *
     * @return int
     */
    private function getCounter(string $counter, int $sessionId): int
    {
        return $this->stash()->get("report.total.$counter")[$sessionId] ?? 0;
    }

    /**
     * @param string $counter
     *
     * @return int
     */
    private function getCounterSum(string $counter): int
    {
        return $this->stash()->get("report.total.$counter")->sum();
    }
}
