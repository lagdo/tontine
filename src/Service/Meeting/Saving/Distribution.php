<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Support\Collection;

use function collect;

class Distribution
{
    /**
     * The savings qualified for profit distribution.
     *
     * @var Collection
     */
    public Collection $selected;

    /**
     * @param Collection $sessions
     * @param Collection $savings
     * @param int $profitAmount
     * @param int $partAmount
     */
    public function __construct(public Collection $sessions, public Collection $savings,
        public int $profitAmount, public int $partAmount = 0)
    {
        $this->selected = collect();
    }
}
