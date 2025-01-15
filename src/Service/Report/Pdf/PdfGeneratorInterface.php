<?php

namespace Siak\Tontine\Service\Report\Pdf;

interface PdfGeneratorInterface
{
    /**
     * @param string $html
     * @param array $config
     *
     * @return string
     */
    public function getPdf(string $html, array $config): string;
}
