<?php

namespace Siak\Tontine\Service\Report\Pdf;

use HeadlessChromium\Browser;

class LocalGenerator implements GeneratorInterface
{
    /**
     * @param Browser $browser
     */
    public function __construct(private Browser $browser)
    {}

    /**
     * @inheritDoc
     */
    public function getPdf(string $html, array $config): string
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
}
