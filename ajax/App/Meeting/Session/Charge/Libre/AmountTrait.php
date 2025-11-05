<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Jaxon\App\Stash\Stash;
use Siak\Tontine\Exception\MessageException;

use function filter_var;
use function str_replace;
use function trans;
use function trim;

trait AmountTrait
{
    /**
     * Get the temp cache
     *
     * @return Stash
     */
    abstract protected function stash(): Stash;

    /**
     * Get an instance of a Jaxon class by name
     *
     * @template T
     * @param class-string<T> $sClassName the class name
     *
     * @return T|null
     */
    abstract protected function cl(string $sClassName): mixed;

    /**
     * @return void
     */
    private function showTotal()
    {
        $this->cl(MemberTotal::class)->render();
        $this->cl(MemberAll::class)->render();
    }

    /**
     * @param string $amount
     * @param bool $required
     *
     * @throws MessageException
     * @return float|int
     */
    private function convertAmount(string $amount): float
    {
        $amount = str_replace(',', '.', trim($amount));
        if($amount !== '' && filter_var($amount, FILTER_VALIDATE_FLOAT) === false)
        {
            throw new MessageException(trans('meeting.errors.amount.invalid', [
                'amount' => $amount,
            ]));
        }

        return $amount === '' ? 0 : (float)$amount;
    }
}
