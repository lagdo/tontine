<?php

namespace Siak\Tontine\Service\Report;

use HeadlessChromium\Browser;

class LocalPdfGenerator implements PdfGeneratorInterface
{
    /**
     * @var Browser
     */
    private $browser;

    /**
     * @var array
     */
    private $config;

    /**
     * @param Browser $browser
     * @param array $config
     */
    public function __construct(Browser $browser, array $config)
    {
        $this->browser = $browser;
        $this->config = $config;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function getPdf(string $html): string
    {
        try
        {
            $page = $this->browser->createPage();
            $page->setHtml($html);
            $pdf = $page->pdf($this->config);

            return $pdf->getBase64();
        }
        finally
        {
            $this->browser->close();
        }
    }
}
