<?php

namespace Ajax\App\Report\Session\Graph;

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
        return $this->flot()->card($this->graphId)->height('300px');
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
     * @inheritDoc
     */
    public function html(): string
    {
        return "<div id=\"{$this->graphId}\"></div>";
    }
}
