<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Subscription;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

class MemberService
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
     * @var ReportService
     */
    protected ReportService $reportService;

    /**
     * @var SessionService
     */
    protected SessionService $sessionService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param ReportService $reportService
     * @param SessionService $sessionService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService,
        ReportService $reportService, SessionService $sessionService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->reportService = $reportService;
        $this->sessionService = $sessionService;
    }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getDeposits(Member $member, Session $session): Collection
    {
        return Subscription::where('member_id', $member->id)
            ->with([
                'pool',
                'receivables' => function($query) use($session) {
                    $query->where('session_id', $session->id)->whereHas('deposit');
                },
            ])
            ->get()
            ->map(function($subscription) {
                $subscription->paid = ($subscription->receivables->count() > 0);
                $subscription->amount = $this->localeService->formatMoney($subscription->pool->amount);
                return $subscription;
            });
    }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getRemitments(Member $member, Session $session): Collection
    {
        return Subscription::where('member_id', $member->id)
            ->whereHas('payable', function($query) use($session) {
                $query->where('session_id', $session->id);
            })
            ->with([
                'pool',
                'payable' => function($query) use($session) {
                    $query->where('session_id', $session->id)->whereHas('remitment');
                },
            ])
            ->get()
            ->map(function($subscription) {
                $subscription->paid = ($subscription->payable !== null);
                $sessionCount = $this->sessionService->enabledSessionCount($subscription->pool);
                $remitmentAmount = $subscription->pool->amount * $sessionCount;
                $subscription->amount = $this->localeService->formatMoney($remitmentAmount);
                return $subscription;
            });
    }
}
