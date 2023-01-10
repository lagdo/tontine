<?php

namespace Siak\Tontine\Service\Report;

interface ReportServiceInterface
{
    /**
     * @param integer $sessionId
     *
     * @return array
     */
    public function getSession(int $sessionId): array;

    /**
     * @param integer $poolId
     *
     * @return array
     */
    public function getPool(int $poolId): array;
}
