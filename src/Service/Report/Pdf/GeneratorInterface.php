<?php

namespace Siak\Tontine\Service\Report\Pdf;

interface GeneratorInterface
{
    /**
     * @param string $template
     *
     * @return string
     */
    public function getSessionReport(string $template): string;

    /**
     * @param string $template
     *
     * @return string
     */
    public function getProfitsReport(string $template): string;

    /**
     * @param string $template
     *
     * @return string
     */
    public function getRoundReport(string $template): string;
}
