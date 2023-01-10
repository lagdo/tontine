<?php

namespace Siak\Tontine\Service\Report;

interface PdfGeneratorInterface
{
    /**
     * @param string $html
     *
     * @return string
     */
    public function getPdf(string $html): string;
}
