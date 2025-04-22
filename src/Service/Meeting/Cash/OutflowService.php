<?php

namespace Siak\Tontine\Service\Meeting\Cash;

use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Category;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Outflow;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function trans;
use function trim;

class OutflowService
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
        return $this->tenantService->guild()->members()->active()
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
        return $this->tenantService->guild()->members()->active()->find($memberId);
    }

    /**
     * Get a list of charges for the dropdown select component.
     *
     * @return Collection
     */
    public function getCharges(): Collection
    {
        return $this->tenantService->guild()->charges()->fee()->active()
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
        return $this->tenantService->guild()->charges()->fee()->find($chargeId);
    }

    /**
     * Get the outflow categories for the dropdown select component.
     *
     * @return Collection
     */
    public function getAccounts(): Collection
    {
        // It is important to call get() before pluck() so the name field is translated.
        $globalCategories = Category::outflow()->get();
        // We need to move the "other" category to the end of the list.
        // getAttributes()['name'] returns the name field, without calling the getter.
        [$otherCategory, $globalCategories] = $globalCategories->partition(fn($category) =>
            $category->getAttributes()['name'] === 'other');

        $guildCategories = $this->tenantService->guild()->categories()->outflow()
            ->active()->get();
        return $globalCategories->concat($guildCategories)->concat($otherCategory)
            ->pluck('name', 'id');
    }

    /**
     * Find a cash outflow category.
     *
     * @param int $categoryId
     *
     * @return Category|null
     */
    public function getAccount(int $categoryId): ?Category
    {
        return Category::outflow()->find($categoryId);
    }

    /**
     * Get the amount available for outflow.
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
     * Get the amount available for outflow.
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
     * Get the amount available for outflow.
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
     * Get the outflows.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getOutflows(int $page = 0): Collection
    {
        return Outflow::with(['member', 'charge', 'category', 'session'])
            ->page($page, $this->tenantService->getLimit())->get();
    }

    /**
     * Get the outflows for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionOutflows(Session $session): Collection
    {
        return $session->outflows()->with(['member', 'charge', 'category'])->get();
    }

    /**
     * Get a cash outflow for a given session.
     *
     * @param Session $session
     * @param int $outflowId
     *
     * @return Outflow|null
     */
    public function getSessionOutflow(Session $session, int $outflowId): ?Outflow
    {
        return $session->outflows()->find($outflowId);
    }

    /**
     * Create a cash outflow.
     *
     * @param Session $session The session
     * @param array $values
     *
     * @return void
     */
    public function createOutflow(Session $session, array $values): void
    {
        $member = $this->getMember($values['member']);
        $charge = $this->getCharge($values['charge']);
        $category = $this->getAccount($values['category']);
        if(!$category)
        {
            throw new MessageException(trans('meeting.category.errors.not_found'));
        }

        $outflow = new Outflow();
        $outflow->amount = $values['amount'];
        $outflow->comment = trim($values['comment']);
        if(($member))
        {
            $outflow->member()->associate($member);
        }
        if(($charge))
        {
            $outflow->charge_lendable = $charge->lendable;
            $outflow->charge()->associate($charge);
        }
        else
        {
            $outflow->charge_lendable = true;
        }
        $outflow->category()->associate($category);
        $outflow->session()->associate($session);
        $outflow->save();
    }

    /**
     * Update a cash outflow.
     *
     * @param Session $session The session
     * @param int $outflowId
     * @param array $values
     *
     * @return void
     */
    public function updateOutflow(Session $session, int $outflowId, array $values): void
    {
        $member = $this->getMember($values['member']);
        $charge = $this->getCharge($values['charge']);
        $category = $this->getAccount($values['category']);
        if(!$category)
        {
            throw new MessageException(trans('meeting.category.errors.not_found'));
        }
        $outflow = $session->outflows()->find($outflowId);
        if(!$outflow)
        {
            throw new MessageException(trans('meeting.outflow.errors.not_found'));
        }

        $outflow->amount = $values['amount'];
        $outflow->comment = trim($values['comment']);
        if(($member))
        {
            $outflow->member()->associate($member);
        }
        else
        {
            $outflow->member()->dissociate();
        }
        if(($charge))
        {
            $outflow->charge_lendable = $charge->lendable;
            $outflow->charge()->associate($charge);
        }
        else
        {
            $outflow->charge_lendable = true;
            $outflow->charge()->dissociate();
        }
        $outflow->category()->associate($category);
        $outflow->save();
    }

    /**
     * Delete a cash outflow.
     *
     * @param Session $session The session
     * @param int $outflowId
     *
     * @return void
     */
    public function deleteOutflow(Session $session, int $outflowId): void
    {
        $session->outflows()->where('id', $outflowId)->delete();
    }
}
