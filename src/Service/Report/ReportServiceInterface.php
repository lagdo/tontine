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
     * @param integer $poolId
     *
     * @return array
     */
    public function getPoolReport(int $poolId): array;
}
