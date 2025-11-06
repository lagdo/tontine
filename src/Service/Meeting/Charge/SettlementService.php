<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;

use function trans;

class SettlementService
{
    /**
     * @param TenantService $tenantService
     * @param BillService $billService
     * @param PaymentServiceInterface $paymentService;
     */
    public function __construct(private TenantService $tenantService,
        private BillService $billService, private PaymentServiceInterface $paymentService)
    {}

    /**
     * Create a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    public function createSettlement(Charge $charge, Session $session, int $billId): void
    {
        $bill = $this->billService->getBill($charge, $session, $billId);
        // Return if the bill is not found or the bill is already settled.
        if(!$bill || ($bill->settlement))
        {
            throw new MessageException(trans('tontine.bill.errors.not_found'));
        }
        $settlement = new Settlement();
        $settlement->bill()->associate($bill);
        $settlement->session()->associate($session);
        $settlement->save();
    }

    /**
     * Delete a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    public function deleteSettlement(Charge $charge, Session $session, int $billId): void
    {
        $bill = $this->billService->getBill($charge, $session, $billId);
        // Return if the bill is not found or the bill is not settled.
        if(!$bill || !($bill->settlement))
        {
            throw new MessageException(trans('tontine.bill.errors.not_found'));
        }
        if((!$this->paymentService->isEditable($bill->settlement)))
        {
            throw new MessageException(trans('tontine.errors.editable'));
        }
        $bill->settlement()->where('session_id', $session->id)->delete();
    }

    /**
     * Create a settlement for all unpaid bills
     *
     * @param Charge $charge
     * @param Session $session
     *
     * @return void
     */
    public function createAllSettlements(Charge $charge, Session $session): void
    {
        $bills = $this->billService->getBills($charge, $session, '', false);
        if($bills->count() === 0)
        {
            return;
        }

        // Todo: use one insert query
        DB::transaction(function() use($bills, $session) {
            foreach($bills as $bill)
            {
                $settlement = new Settlement();
                $settlement->bill()->associate($bill);
                $settlement->session()->associate($session);
                $settlement->save();
            }
        });
    }

    /**
     * Delete all settlements
     *
     * @param Charge $charge
     * @param Session $session
     *
     * @return void
     */
    public function deleteAllSettlements(Charge $charge, Session $session): void
    {
        $bills = $this->billService->getBills($charge, $session, '', true)
            ->filter(fn($bill) => $this->paymentService->isEditable($bill->settlement));
        if($bills->count() === 0)
        {
            return;
        }

        Settlement::whereIn('bill_id', $bills->pluck('id'))
            ->where('session_id', $session->id)
            ->delete();
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return array<int>
     */
    public function getSettlementTotal(Charge $charge, Session $session): array
    {
        return $this->billService->getSettlementTotal($charge, $session);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return Bill
     */
    public function getSettlementCount(Charge $charge, Session $session): Bill
    {
        return $this->billService->getSettlementAmount($charge, $session);
    }
}
