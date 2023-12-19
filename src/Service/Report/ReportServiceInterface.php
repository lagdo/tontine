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
     * @param integer $sessionId
     *
     * @return array
     */
    public function getProfitReport(int $sessionId): array;

    /**
     * @param int $roundId
     *
     * @return array
     */
    public function getRoundReport(int $roundId): array;
}
