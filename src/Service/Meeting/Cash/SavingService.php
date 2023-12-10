<?php

namespace Siak\Tontine\Service\Meeting\Cash;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Saving;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

use function trans;

class SavingService
{
    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param FundService $fundService
     */
    public function __construct(private LocaleService $localeService,
        private TenantService $tenantService, private FundService $fundService)
    {}

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->getSession($sessionId);
    }

    /**
     * Get a list of members for the dropdown select component.
     *
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->tenantService->tontine()->members()
            ->orderBy('name', 'asc')->pluck('name', 'id');
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
     * Get the savings.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getSavings(int $page = 0): Collection
    {
        return Saving::with(['member', 'session', 'fund'])
            ->page($page, $this->tenantService->getLimit())->get();
    }

    /**
     * Get the savings for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionSavings(Session $session): Collection
    {
        return $session->savings()->with(['member', 'fund'])->get();
    }

    /**
     * Get a saving for a given session.
     *
     * @param Session $session
     * @param int $savingId
     *
     * @return Saving|null
     */
    public function getSessionSaving(Session $session, int $savingId): ?Saving
    {
        return $session->savings()->with(['member', 'fund'])->find($savingId);
    }

    /**
     * Create a saving.
     *
     * @param Session $session The session
     * @param array $values
     *
     * @return void
     */
    public function createSaving(Session $session, array $values): void
    {
        $fund = $values['fund_id'] === 0 ? null :
            $this->fundService->getFund($values['fund_id']);
        $member = $this->getMember($values['member']);
        if(!$member)
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        $saving = new Saving();
        $saving->amount = $values['amount'];
        $saving->member()->associate($member);
        $saving->session()->associate($session);
        if($fund !== null)
        {
            $saving->fund()->associate($fund);
        }
        $saving->save();
    }

    /**
     * Update a saving.
     *
     * @param Session $session The session
     * @param array $values
     *
     * @return void
     */
    public function updateSaving(Session $session, int $savingId, array $values): void
    {
        $fund = $values['fund_id'] === 0 ? null :
            $this->fundService->getFund($values['fund_id']);
        $member = $this->getMember($values['member']);
        if(!$member)
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }
        $saving = $session->savings()->find($savingId);
        if(!$saving)
        {
            throw new MessageException(trans('meeting.saving.errors.not_found'));
        }

        $saving->amount = $values['amount'];
        $saving->member()->associate($member);
        if($fund !== null)
        {
            $saving->fund()->associate($fund);
        }
        else
        {
            $saving->fund()->dissociate();
        }
        $saving->save();
    }

    /**
     * Delete a saving.
     *
     * @param Session $session The session
     * @param int $savingId
     *
     * @return void
     */
    public function deleteSaving(Session $session, int $savingId): void
    {
        $session->savings()->where('id', $savingId)->delete();
    }
}