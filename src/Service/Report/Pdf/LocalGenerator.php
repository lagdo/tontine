<?php

namespace Siak\Tontine\Service\Report\Pdf;

use HeadlessChromium\Browser;

use function trim;
use function view;

class LocalGenerator implements GeneratorInterface
{
    /**
     * @param Browser $browser
     * @param array $config
     */
    public function __construct(private Browser $browser, private array $config)
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
     * @param string $html
     * @param array $config
     *
     * @return string
     */
    protected function getPdf(string $html, array $config): string
    {
        try
        {
            $page = $this->browser->createPage();
            $page->setHtml($html);
            return $page->pdf($config)->getBase64();
        }
        finally
        {
            $this->browser->close();
        }
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function getSessionReport(string $template): string
    {
        return $this->getPdf('' . view("tontine.report.$template.session"),
            $this->getSessionReportConfig($this->config, $template));
    }

    /**
     * @param string $template
     *
     * @return string
     */
    public function getRoundReport(string $template): string
    {
        return $this->getPdf('' . view("tontine.report.$template.round"),
            $this->getRoundReportConfig($this->config, $template));
    }
}
