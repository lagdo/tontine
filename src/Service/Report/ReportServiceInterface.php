<?php

namespace Siak\Tontine\Service\Report;

interface ReportServiceInterface
{
    /**
     * @param integer $sessionId
     *
     * @return array
     */
    public function getSessionReport(int $sessionId): array;

    /**
     * @return array
     */
    public function getRoundReport(): array;
}
