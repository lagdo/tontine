<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Funding;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

use function trans;

class FundingService
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
     * Get the fundings.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getFundings(int $page = 0): Collection
    {
        return Funding::with(['member', 'session'])
            ->page($page, $this->tenantService->getLimit())->get();
    }

    /**
     * Get the fundings for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionFundings(Session $session): Collection
    {
        return $session->fundings()->with(['member'])->get();
    }

    /**
     * Get a funding for a given session.
     *
     * @param Session $session
     * @param int $fundingId
     *
     * @return Funding|null
     */
    public function getSessionFunding(Session $session, int $fundingId): ?Funding
    {
        return $session->fundings()->with(['member'])->find($fundingId);
    }

    /**
     * Create a funding.
     *
     * @param Session $session The session
     * @param int $memberId
     * @param int $amount
     *
     * @return void
     */
    public function createFunding(Session $session, int $memberId, int $amount): void
    {
        $member = $this->getMember($memberId);
        if(!$member)
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }

        $funding = new Funding();
        $funding->amount = $amount;
        $funding->member()->associate($member);
        $funding->session()->associate($session);
        $funding->save();
    }

    /**
     * Update a funding.
     *
     * @param Session $session The session
     * @param int $fundingId
     * @param int $amount
     *
     * @return void
     */
    public function updateFunding(Session $session, int $fundingId, int $amount): void
    {
        $session->fundings()->where('id', $fundingId)->update(['amount' => $amount]);
    }

    /**
     * Delete a funding.
     *
     * @param Session $session The session
     * @param int $fundingId
     *
     * @return void
     */
    public function deleteFunding(Session $session, int $fundingId): void
    {
        $session->fundings()->where('id', $fundingId)->delete();
    }
}
