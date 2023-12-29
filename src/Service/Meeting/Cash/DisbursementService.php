<?php

namespace Siak\Tontine\Service\Meeting\Cash;

use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Category;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Disbursement;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function trans;
use function trim;

class DisbursementService
{
    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, protected BalanceCalculator $balanceCalculator)
    {}

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
        return $this->tenantService->tontine()->members()->active()->find($memberId);
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
        $globalCategories = Category::disbursement()->get();
        // We need to move the "other" category to the end of the list.
        // getAttributes()['name'] returns the name field, without calling the getter.
        [$otherCategory, $globalCategories] = $globalCategories->partition(fn($category) =>
            $category->getAttributes()['name'] === 'other');

        $tontineCategories = $this->tenantService->tontine()->categories()->disbursement()
            ->active()->get();
        return $globalCategories->concat($tontineCategories)->concat($otherCategory)
            ->pluck('name', 'id');
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
        return $this->balanceCalculator->getTotalBalance($session);
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
