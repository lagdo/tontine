<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\FineBill;
use Siak\Tontine\Model\RoundBill;
use Siak\Tontine\Model\SessionBill;
use Siak\Tontine\Model\TontineBill;
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

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    private function getTontineFees(Member $member, Session $session): Collection
    {
        return TontineBill::with(['bill', 'bill.settlement'])
            ->where('member_id', $member->id)
            ->where(function($query) use($session) {
                return $query
                    // Fees that are not yet settled.
                    ->orWhere(function(Builder $query) {
                        return $query->whereDoesntHave('bill.settlement');
                    })
                    // Fees settled on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('bill.settlement', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
            })
            ->get();
    }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    private function getRoundFees(Member $member, Session $session): Collection
    {
        return RoundBill::with(['bill', 'bill.settlement'])
            ->where('member_id', $member->id)
            ->where('round_id', $session->round_id)
            ->where(function($query) use($session) {
                return $query
                    // Fees that are not yet settled.
                    ->orWhere(function(Builder $query) {
                        return $query->whereDoesntHave('bill.settlement');
                    })
                    // Fees settled on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('bill.settlement', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
            })
            ->get();
    }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    private function getSessionFees(Member $member, Session $session): Collection
    {
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');
        return SessionBill::with(['bill', 'bill.settlement', 'session'])
            ->where('member_id', $member->id)
            ->whereIn('session_id', $sessionIds)
            ->where(function($query) use($session) {
                return $query
                    // Fees on this session.
                    ->where('session_id', $session->id)
                    // Fees that are not yet settled.
                    ->orWhere(function(Builder $query) {
                        return $query->whereDoesntHave('bill.settlement');
                    })
                    // Fees settled on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('bill.settlement', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
            })
            ->get();
    }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getFees(Member $member, Session $session): Collection
    {
        return $this->getTontineFees($member, $session)
            ->merge($this->getRoundFees($member, $session))
            ->merge($this->getSessionFees($member, $session))
            ->map(function($fee) {
                $fee->paid = ($fee->bill->settlement !== null);
                $fee->amount = $this->localeService->formatMoney($fee->bill->amount);
                return $fee;
            });
    }

    /**
     * @param Member $member
     * @param Session $session
     *
     * @return Collection
     */
    public function getFines(Member $member, Session $session): Collection
    {
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');
        return FineBill::with(['bill', 'bill.settlement', 'session'])
            ->where('member_id', $member->id)
            ->whereIn('session_id', $sessionIds)
            ->where(function($query) use($session) {
                return $query
                    // Fines given on this session.
                    ->where('session_id', $session->id)
                    // Fines that are not yet settled.
                    ->orWhere(function(Builder $query) {
                        return $query->whereDoesntHave('bill.settlement');
                    })
                    // Fines settled on this session.
                    ->orWhere(function(Builder $query) use($session) {
                        return $query->whereHas('bill.settlement', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
            })
            ->get()
            ->map(function($fine) {
                $fine->paid = ($fine->bill->settlement !== null);
                $fine->amount = $this->localeService->formatMoney($fine->bill->amount);
                return $fine;
            });
    }
}
