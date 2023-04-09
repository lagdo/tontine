<?php

namespace Siak\Tontine\Service\Meeting;

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
     * Get the fundings for a given session.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getFundings(int $page = 0): Collection
    {
        $fundings = Funding::with(['member', 'session']);
        if($page > 0 )
        {
            $fundings->take($this->tenantService->getLimit());
            $fundings->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $fundings->get();
    }

    /**
     * Get the amount available for funding.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionFundings(Session $session): Collection
    {
        $fundings = $session->fundings()->with(['member'])->get();
        $fundings->each(function($funding) {
            $funding->amount = $this->localeService->formatMoney($funding->amount);
        });
        return $fundings;
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
