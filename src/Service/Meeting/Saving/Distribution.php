<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Support\Collection;

class Distribution
{
    /**
     * @param Collection $savings
     * @param int $partValue
     */
    public function __construct(public Collection $savings, public int $partValue = 0)
    {}
}
