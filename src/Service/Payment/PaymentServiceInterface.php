<?php

namespace Siak\Tontine\Service\Payment;

use Illuminate\Database\Eloquent\Model;

interface PaymentServiceInterface
{
    /**
     * @param Model $item An item that can be paid
     *
     * @return bool
     */
    public function isEditable(Model $item): bool;
}
