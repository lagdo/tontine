<?php

namespace Siak\Tontine\Service\Meeting\Cash;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Category;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Disbursement;
use Siak\Tontine\Model\Funding;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\PartialRefund;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Settlement;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function trans;
use function trim;

class DisbursementService
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * Get a list of members for the dropdown select component.
     *
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->tenantService->tontine()->members()->active()
            ->orderBy('name', 'asc')->pluck('name', 'id')->prepend('', 0);
    }

    /**
     * Find a member.
     *
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(int $memberId): ?Member
    {
        return $this->tenantService->tontine()->members()->find($memberId);
    }

    /**
     * Get a list of charges for the dropdown select component.
     *
     * @return Collection
     */
    public function getCharges(): Collection
    {
        return $this->tenantService->tontine()->charges()->fee()->active()
            ->orderBy('name', 'asc')->pluck('name', 'id')->prepend('', 0);
    }

    /**
     * Find a charge.
     *
     * @param int $chargeId
     *
     * @return Charge|null
     */
    public function getCharge(int $chargeId): ?Charge
    {
        return $this->tenantService->tontine()->charges()->fee()->find($chargeId);
    }

    /**
     * Get the disbursement categories for the dropdown select component.
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        // It is important to call get() before pluck() so the name field is translated.
        return Category::disbursement()->get()->pluck('name', 'id');
    }

    /**
     * Find a disbursement category.
     *
     * @param int $categoryId
     *
     * @return Category|null
     */
    public function getCategory(int $categoryId): ?Category
    {
        return Category::disbursement()->find($categoryId);
    }

    /**
     * Get the amount available for disbursement.
     *
     * @param Session $session    The session
     *
     * @return int
     */
    public function getAmountAvailable(Session $session): int
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->getPreviousSessions($session);

        // The amount available for lending is the sum of the settlements and refunds,
        // minus the sum of the loans and disbursements, for all the sessions until the selected.
        $settlement = Settlement::select(DB::raw('sum(bills.amount) as total'))
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->whereIn('settlements.session_id', $sessionIds)
            ->value('total');
        $refund = Refund::select(DB::raw('sum(debts.amount) as total'))
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->whereIn('refunds.session_id', $sessionIds)
            ->value('total');
        // Partial refunds on debts that are not yet refunded.
        $partialRefund = PartialRefund::select(DB::raw('sum(partial_refunds.amount) as total'))
            ->join('debts', 'partial_refunds.debt_id', '=', 'debts.id')
            ->whereIn('partial_refunds.session_id', $sessionIds)
            ->whereNotExists(function (Builder $query) {
                $query->select(DB::raw(1))->from('refunds')
                    ->whereColumn('refunds.debt_id', 'debts.id');
            })
            ->value('total');
        $debt = Debt::principal()->select(DB::raw('sum(debts.amount) as total'))
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->whereIn('loans.session_id', $sessionIds)
            ->value('total');
        $disbursement = Disbursement::select(DB::raw('sum(amount) as total'))
            ->whereIn('session_id', $sessionIds)
            ->value('total');

        return $settlement + $refund + $partialRefund - $debt - $disbursement;
    }

    /**
     * Get the amount available for disbursement.
     *
     * @param Session $session    The session
     *
     * @return float
     */
    public function getAmountAvailableValue(Session $session): float
    {
        return $this->localeService->getMoneyValue($this->getAmountAvailable($session));
    }

    /**
     * Get the amount available for disbursement.
     *
     * @param Session $session    The session
     *
     * @return string
     */
    public function getFormattedAmountAvailable(Session $session): string
    {
        return $this->localeService->formatMoney($this->getAmountAvailable($session));
    }

    /**
     * Get the disbursements.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getDisbursements(int $page = 0): Collection
    {
        return Disbursement::with(['member', 'charge', 'category', 'session'])
            ->page($page, $this->tenantService->getLimit())->get();
    }

    /**
     * Get the disbursements for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionDisbursements(Session $session): Collection
    {
        return $session->disbursements()->with(['member', 'charge', 'category'])->get();
    }

    /**
     * Get a disbursement for a given session.
     *
     * @param Session $session
     * @param int $disbursementId
     *
     * @return Disbursement|null
     */
    public function getSessionDisbursement(Session $session, int $disbursementId): ?Disbursement
    {
        return $session->disbursements()->find($disbursementId);
    }

    /**
     * Create a disbursement.
     *
     * @param Session $session The session
     * @param array $values
     *
     * @return void
     */
    public function createDisbursement(Session $session, array $values): void
    {
        $member = $this->getMember($values['member']);
        $charge = $this->getCharge($values['charge']);
        $category = $this->getCategory($values['category']);
        if(!$category)
        {
            throw new MessageException(trans('meeting.category.errors.not_found'));
        }

        $disbursement = new Disbursement();
        $disbursement->amount = $values['amount'];
        $disbursement->comment = trim($values['comment']);
        if(($member))
        {
            $disbursement->member()->associate($member);
        }
        if(($charge))
        {
            $disbursement->charge_lendable = $charge->lendable;
            $disbursement->charge()->associate($charge);
        }
        else
        {
            $disbursement->charge_lendable = true;
        }
        $disbursement->category()->associate($category);
        $disbursement->session()->associate($session);
        $disbursement->save();
    }

    /**
     * Update a disbursement.
     *
     * @param Session $session The session
     * @param int $disbursementId
     * @param array $values
     *
     * @return void
     */
    public function updateDisbursement(Session $session, int $disbursementId, array $values): void
    {
        $member = $this->getMember($values['member']);
        $charge = $this->getCharge($values['charge']);
        $category = $this->getCategory($values['category']);
        if(!$category)
        {
            throw new MessageException(trans('meeting.category.errors.not_found'));
        }
        $disbursement = $session->disbursements()->find($disbursementId);
        if(!$disbursement)
        {
            throw new MessageException(trans('meeting.disbursement.errors.not_found'));
        }

        $disbursement->amount = $values['amount'];
        $disbursement->comment = trim($values['comment']);
        if(($member))
        {
            $disbursement->member()->associate($member);
        }
        else
        {
            $disbursement->member()->dissociate();
        }
        if(($charge))
        {
            $disbursement->charge_lendable = $charge->lendable;
            $disbursement->charge()->associate($charge);
        }
        else
        {
            $disbursement->charge_lendable = true;
            $disbursement->charge()->dissociate();
        }
        $disbursement->category()->associate($category);
        $disbursement->save();
    }

    /**
     * Delete a disbursement.
     *
     * @param Session $session The session
     * @param int $disbursementId
     *
     * @return void
     */
    public function deleteDisbursement(Session $session, int $disbursementId): void
    {
        $session->disbursements()->where('id', $disbursementId)->delete();
    }
}
