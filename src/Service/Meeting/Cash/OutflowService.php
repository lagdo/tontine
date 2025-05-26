<?php

namespace Siak\Tontine\Service\Meeting\Cash;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Category;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Outflow;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Payment\BalanceCalculator;
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
     * @param Round $round
     *
     * @return Collection
     */
    public function getMembers(Round $round): Collection
    {
        return $round->members()
            ->orderBy('name', 'asc')
            ->get()
            ->pluck('name', 'id')
            ->prepend('', 0);
    }

    /**
     * Find a member.
     *
     * @param Round $round
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(Round $round, int $memberId): ?Member
    {
        return $round->members()->find($memberId);
    }

    /**
     * Get a list of charges for the dropdown select component.
     *
     * @param Round $round
     *
     * @return Collection
     */
    public function getCharges(Round $round): Collection
    {
        return $round->charges()->fee()
            ->orderBy('name', 'asc')
            ->get()
            ->pluck('name', 'id')
            ->prepend('', 0);
    }

    /**
     * Find a charge.
     *
     * @param Round $round
     * @param int $chargeId
     *
     * @return Charge|null
     */
    public function getCharge(Round $round, int $chargeId): ?Charge
    {
        return $round->charges()->fee()->find($chargeId);
    }

    /**
     * Get the outflow categories for the dropdown select component.
     *
     * @param Guild $guild
     *
     * @return Builder
     */
    private function getAccountQuery(Guild $guild): Builder
    {
        return Category::outflow()->active()
            ->where(fn($query) => $query->global()->orWhere('guild_id', $guild->id));
    }

    /**
     * Get the outflow categories for the dropdown select component.
     *
     * @param Guild $guild
     *
     * @return Collection
     */
    public function getAccounts(Guild $guild): Collection
    {
        // It is important to call get() before pluck() so the name field is translated.
        $categories = $this->getAccountQuery($guild)->orderBy('id')->get();
        // We need to move the "other" category to the end of the list.
        // getAttributes()['name'] returns the name field, without calling the getter.
        [$otherCategory, $categories] = $categories
            ->partition(fn($category) => $category->is_other);

        return $categories->concat($otherCategory)->pluck('name', 'id');
    }

    /**
     * Find a cash outflow category.
     *
     * @param Guild $guild
     * @param int $categoryId
     *
     * @return Category|null
     */
    public function getAccount(Guild $guild, int $categoryId): ?Category
    {
        return $this->getAccountQuery($guild)->find($categoryId);
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
     * Count the outflows for a given session.
     *
     * @param Session $session
     *
     * @return int
     */
    public function getSessionOutflowCount(Session $session): int
    {
        return $session->outflows()->count();
    }

    /**
     * Get the outflows for a given session.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getSessionOutflows(Session $session, int $page = 0): Collection
    {
        return $session->outflows()
            ->with(['member', 'charge', 'category'])
            ->page($page, $this->tenantService->getLimit())
            ->get();
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
     * @param Guild $guild
     * @param Session $session The session
     * @param array $values
     *
     * @return void
     */
    public function createOutflow(Guild $guild, Session $session, array $values): void
    {
        $member = $this->getMember($session->round, $values['member']);
        $charge = $this->getCharge($session->round, $values['charge']);
        $category = $this->getAccount($guild, $values['category']);
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
     * @param Guild $guild
     * @param Session $session The session
     * @param int $outflowId
     * @param array $values
     *
     * @return void
     */
    public function updateOutflow(Guild $guild, Session $session, int $outflowId, array $values): void
    {
        $member = $this->getMember($session->round, $values['member']);
        $charge = $this->getCharge($session->round, $values['charge']);
        $category = $this->getAccount($guild, $values['category']);
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
