<?php

namespace Siak\Tontine\Service\Report\Pdf;

use function view;

class PrinterService
{
    /**
     * @param array $config
     */
    public function __construct(private array $config)
    {}

    /**
     * @param string $templatePath
     * @param array $config
     *
     * @return string
     */
    private function getPdf(string $templatePath, array $config = []): string
    {
        $config = [
            ...$this->config,
            ...$config,
            'headerTemplate' => (string)view("$templatePath.tpl.header"),
            'footerTemplate' => (string)view("$templatePath.tpl.footer"),
        ];
        return GeneratorFacade::getPdf((string)view($templatePath), $config);
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function getSessionReport(string $template): string
    {
        return $this->getPdf("tontine.report.$template.session");
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function getProfitsReport(string $template): string
    {
        return $this->getPdf("tontine.report.$template.profits");
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function getRoundReport(string $template): string
    {
        return $this->getPdf("tontine.report.$template.round");
    }
}
