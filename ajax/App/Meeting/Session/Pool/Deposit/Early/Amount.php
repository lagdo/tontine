<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit\Early;

class Amount extends \Ajax\App\Meeting\Session\Pool\Deposit\Amount
{
    /**
     * @var string
     */
    protected string $amountFuncClass = AmountFunc::class;
}
