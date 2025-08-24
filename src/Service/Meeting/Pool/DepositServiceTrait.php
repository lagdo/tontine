<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\DepositReal;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;

use function trans;

trait DepositServiceTrait
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PaymentServiceInterface
     */
    protected PaymentServiceInterface $paymentService;

    /**
     * @param Builder|Relation
     * @param int $page
     *
     * @return Builder|Relation
     */
    public function getReceivableDetailsQuery(Builder|Relation $query,
        int $page = 0): Builder|Relation
    {
        return $query
            ->select('receivables.*', DB::raw('pd.amount, member_defs.name as member'))
            ->join('pools', 'pools.id', '=', 'subscriptions.pool_id')
            ->join(DB::raw('pool_defs as pd'), 'pools.def_id', '=', 'pd.id')
            ->join('members', 'members.id', '=', 'subscriptions.member_id')
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->with(['deposit'])
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('member_defs.name', 'asc')
            ->orderBy('subscriptions.id', 'asc');
    }

    /**
     * Create a deposit.
     *
     * @param Receivable $receivable
     * @param Session $session The session
     * @param int $amount
     *
     * @return void
     */
    protected function saveDeposit(Receivable $receivable, Session $session, int $amount = 0): void
    {
        if($receivable->deposit !== null)
        {
            // The deposit exists. It is then modified.
            DepositReal::where(['id' => $receivable->deposit->id])
                ->update(['amount' => $amount]);
            return;
        }

        $deposit = new DepositReal();
        $deposit->amount = $amount;
        $deposit->receivable()->associate($receivable);
        $deposit->session()->associate($session);
        $deposit->save();
    }

    /**
     * @param Pool $pool The pool
     * @param Receivable|null $receivable
     * @param int $amount
     *
     * @return void
     */
    protected function checkDepositCreation(Pool $pool, ?Receivable $receivable, int $amount = 0): void
    {
        if(!$receivable)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if($pool->deposit_fixed && $receivable->deposit !== null)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if(!$pool->deposit_fixed)
        {
            if($amount <= 0)
            {
                throw new MessageException(trans('tontine.subscription.errors.amount'));
            }
            if($receivable->deposit !== null &&
                !$this->paymentService->isEditable($receivable->deposit))
            {
                throw new MessageException(trans('tontine.errors.editable'));
            }
        }
    }

    /**
     * Delete a deposit.
     *
     * @param Receivable|null $receivable
     *
     * @return void
     */
    protected function _deleteDeposit(?Receivable $receivable): void
    {
        if(!$receivable || !$receivable->deposit)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if(!$this->paymentService->isEditable($receivable->deposit))
        {
            throw new MessageException(trans('tontine.errors.editable'));
        }
        $receivable->deposit_real()->delete();
    }
}
