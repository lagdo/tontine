<?php

namespace Siak\Tontine\Service\Report\Pdf;

use function trim;
use function view;

class PrinterService
{
    /**
     * @param array $config
     */
    public function __construct(private array $config)
    {}

    /**
     * @param array $config
     * @param string $template
     *
     * @return array
     */
    protected function getSessionReportConfig(array $config, string $template): array
    {
        return [
            ...$config,
            'marginTop' => 0.8,
            'marginBottom' => 0.6,
            'headerTemplate' => trim('' . view("tontine.report.$template.session.tpl.header")),
            'footerTemplate' => trim('' . view("tontine.report.$template.session.tpl.footer")),
        ];
    }

    /**
     * @param array $config
     * @param string $template
     *
     * @return array
     */
    protected function getProfitsReportConfig(array $config, string $template): array
    {
        return [
            ...$config,
            'marginTop' => 0.8,
            'marginBottom' => 0.6,
            'headerTemplate' => trim('' . view("tontine.report.$template.profits.tpl.header")),
            'footerTemplate' => trim('' . view("tontine.report.$template.profits.tpl.footer")),
        ];
    }

    /**
     * @param array $config
     * @param string $template
     *
     * @return array
     */
    protected function getRoundReportConfig(array $config, string $template): array
    {
        return [
            ...$config,
            'marginTop' => 0.8,
            'marginBottom' => 0.6,
            'headerTemplate' => trim('' . view("tontine.report.$template.round.tpl.header")),
            'footerTemplate' => trim('' . view("tontine.report.$template.round.tpl.footer")),
        ];
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function getSessionReport(string $template): string
    {
        return GeneratorFacade::getPdf('' . view("tontine.report.$template.session"),
            $this->getSessionReportConfig($this->config, $template));
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function getProfitsReport(string $template): string
    {
        return GeneratorFacade::getPdf('' . view("tontine.report.$template.profits"),
            $this->getProfitsReportConfig($this->config, $template));
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function getRoundReport(string $template): string
    {
        return GeneratorFacade::getPdf('' . view("tontine.report.$template.round"),
            $this->getRoundReportConfig($this->config, $template));
    }
}
