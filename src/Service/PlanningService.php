<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Fund;
use stdClass;

use function collect;
use function compact;
use function floor;
use function gmp_gcd;

class PlanningService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return stdClass
     */
    private function makeFigures($defaultValue): stdClass
    {
        $figures = new stdClass();

        $figures->cashier = new stdClass();
        $figures->cashier->start = $defaultValue;
        $figures->cashier->recv = $defaultValue;
        $figures->cashier->end = $defaultValue;

        $figures->deposit = new stdClass();
        $figures->deposit->count = $defaultValue;
        $figures->deposit->amount = $defaultValue;

        $figures->remittance = new stdClass();
        $figures->remittance->count = $defaultValue;
        $figures->remittance->amount = $defaultValue;

        return $figures;
    }

    /**
     * @param stdClass $figures
     *
     * @return stdClass
     */
    private function formatCurrencies(stdClass $figures): stdClass
    {
        $figures->cashier->start = Currency::format($figures->cashier->start, true);
        $figures->cashier->recv = Currency::format($figures->cashier->recv, true);
        $figures->cashier->end = Currency::format($figures->cashier->end, true);
        $figures->deposit->amount = Currency::format($figures->deposit->amount, true);
        $figures->remittance->amount = Currency::format($figures->remittance->amount, true);

        return $figures;
    }

    /**
     * @param Fund $fund
     * @param Collection $sessions
     * @param Collection $subscriptions
     *
     * @return array
     */
    private function getExpectedFigures(Fund $fund, Collection $sessions, Collection $subscriptions): array
    {
        $sessionCount = $sessions->filter(function($session) use($fund) {
            return $session->enabled($fund);
        })->count();
        $subscriptionCount = $fund->subscriptions()->count();
        $depositCount = $subscriptions->count();

        $remittanceAmount = $fund->amount * $sessionCount;
        $depositAmount = $fund->amount * $subscriptions->count();

        $rank = 0;
        $cashier = 0;
        $expectedFigures = [];
        foreach($sessions as $session)
        {
            if($session->disabled($fund))
            {
                $expectedFigures[$session->id] = $this->makeFigures('');
                continue;
            }

            $figures = $this->makeFigures(0);

            $figures->cashier->start = $cashier;
            $figures->cashier->recv = $cashier + $depositAmount;
            $figures->deposit->count = $depositCount;
            $figures->deposit->amount = $depositAmount;
            $figures->remittance->count =
                $this->getRemittanceCount($sessionCount, $subscriptionCount, $rank++);
            $figures->remittance->amount = $remittanceAmount * $figures->remittance->count;
            $figures->cashier->end = $cashier + $depositAmount - $figures->remittance->amount;
            $cashier = $figures->cashier->end;

            $expectedFigures[$session->id] = $this->formatCurrencies($figures);
        }

        return $expectedFigures;
    }

    /**
     * @param Fund $fund
     * @param Collection $sessions
     * @param Collection $subscriptions
     *
     * @return array
     */
    private function getCollectedFigures(Fund $fund, Collection $sessions, Collection $subscriptions): array
    {
        $cashier = 0;
        $remittanceAmount = $fund->amount * $sessions->filter(function($session) use($fund) {
            return $session->enabled($fund);
        })->count();

        $collectedFigures = [];
        foreach($sessions as $session)
        {
            if($session->disabled($fund) || $session->pending)
            {
                $collectedFigures[$session->id] = $this->makeFigures('&nbsp;');
                continue;
            }

            $figures = $this->makeFigures(0);
            $figures->cashier->start = $cashier;
            $figures->cashier->recv = $cashier;
            foreach($subscriptions as $subscription)
            {
                if(($subscription->receivables[$session->id]->deposit))
                {
                    $figures->deposit->count++;
                    $figures->deposit->amount += $fund->amount;
                    $figures->cashier->recv += $fund->amount;
                }
            }
            $figures->cashier->end = $figures->cashier->recv;
            foreach($session->payables as $payable)
            {
                if(($payable->remittance))
                {
                    $figures->remittance->count++;
                    $figures->remittance->amount += $remittanceAmount;
                    $figures->cashier->end -= $remittanceAmount;
                }
            }

            $cashier = $figures->cashier->end;
            $collectedFigures[$session->id] = $this->formatCurrencies($figures);
        }

        return $collectedFigures;
    }

    /**
     * Get the receivables of a given fund.
     *
     * Will return basic data on subscriptions.
     *
     * @param Fund $fund
     *
     * @return array
     */
    public function getReceivables(Fund $fund): array
    {
        $sessions = $this->tenantService->round()->sessions()->get();
        $subscriptions = $fund->subscriptions()->with(['member'])->get();
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($fund, $sessions, $subscriptions);

        return compact('fund', 'sessions', 'subscriptions', 'figures');
    }

    /**
     * Get the payables of a given fund.
     *
     * @param Fund $fund
     * @param array $with
     *
     * @return Collection
     */
    private function _getSessions(Fund $fund, array $with = []): Collection
    {
        $with['payables'] = function($query) use($fund) {
            // Keep only the subscriptions of the current fund.
            $query->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
                ->where('subscriptions.fund_id', $fund->id);
        };
        return $this->tenantService->round()->sessions()->with($with)->get();
    }

    /**
     * Get the payables of a given fund.
     *
     * @param Fund $fund
     *
     * @return array
     */
    public function getPayables(Fund $fund): array
    {
        $sessions = $this->_getSessions($fund, ['payables.subscription']);
        $subscriptions = $fund->subscriptions()->with(['payable', 'member'])->get();
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($fund, $sessions, $subscriptions);

        // Set the subscriptions that will be pay at each session.
        // Pad with 0's when the beneficiaries are not yet set.
        $sessions->each(function($session) use($figures, $fund) {
            if($session->disabled($fund))
            {
                return;
            }
            // Pick the subscriptions ids, and fill with 0's to the max available.
            $session->beneficiaries = $session->payables->map(function($payable) {
                return $payable->subscription_id;
            })->pad($figures->expected[$session->id]->remittance->count, 0);
        });

        // Separate subscriptions that already have a beneficiary assigned from the others.
        [$subscriptions, $beneficiaries] = $subscriptions->partition(function($subscription) {
            return !$subscription->payable->session_id;
        });
        $beneficiaries = $beneficiaries->pluck('member.name', 'id');
        // Show the list of subscriptions only for mutual tontines
        if($this->tenantService->tontine()->is_mutual)
        {
            $subscriptions = $subscriptions->pluck('member.name', 'id');
            $subscriptions->prepend('', 0);
        }
        else // if($this->tenantService->tontine()->is_financial)
        {
            $subscriptions = collect([]);
        }

        return compact('fund', 'sessions', 'subscriptions', 'beneficiaries', 'figures');
    }

    /**
     * Get the receivables of a given fund.
     *
     * Will return extended data on subscriptions.
     *
     * @param Fund $fund
     *
     * @return array
     */
    public function getFigures(Fund $fund): array
    {
        $subscriptions = $fund->subscriptions()->with(['member', 'receivables.deposit'])
            ->get()->each(function($subscription) {
                $subscription->setRelation('receivables', $subscription->receivables->keyBy('session_id'));
            });
        $sessions = $this->_getSessions($fund, ['payables.remittance']);
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($fund, $sessions, $subscriptions);
        $figures->collected = $this->getCollectedFigures($fund, $sessions, $subscriptions);

        return compact('fund', 'sessions', 'subscriptions', 'figures');
    }

    /**
     * Get the number of subscribers to remit a fund to at a given session
     *
     * @param int $sessionCount
     * @param int $subscriptionCount
     * @param int $sessionRank
     *
     * @return int
     */
    public function getRemittanceCount(int $sessionCount, int $subscriptionCount, int $sessionRank): int
    {
        if($sessionCount === 0 || $subscriptionCount === 0)
        {
            return 0;
        }

        // Greatest common divisor
        $gcd = (int)gmp_gcd($sessionCount, $subscriptionCount);
        $sessionsInLoop = (int)($sessionCount / $gcd);
        $subscriptionsInLoop = (int)($subscriptionCount / $gcd);

        // The session rank in a loop, ranging from 0 to $sessionInLoop - 1.
        $sessionRankInLoop = $sessionRank % $sessionsInLoop;
        $extraSubscriptionsInLoop = $subscriptionsInLoop % $sessionsInLoop;
        return (int)floor($subscriptionCount / $sessionCount) +
            ($sessionRankInLoop < $sessionsInLoop - $extraSubscriptionsInLoop ? 0 : 1);
    }

    /**
     * @param Fund $fund
     * @param int $sessionId
     *
     * @return array|stdClass
     */
    public function getRemittanceFigures(Fund $fund, int $sessionId = 0)
    {
        $sessions = $this->_getSessions($fund, ['payables.subscription.member']);
        $sessionCount = $sessions->filter(function($session) use($fund) {
            return $session->enabled($fund);
        })->count();
        $subscriptionCount = $fund->subscriptions()->count();
        $remittanceAmount = $fund->amount * $sessionCount;
        $formattedAmount = Currency::format($remittanceAmount);

        $figures = [];
        $rank = 0;
        foreach($sessions as $session)
        {
            $figures[$session->id] = new stdClass();
            $figures[$session->id]->payables = $session->payables;
            $figures[$session->id]->count = 0;
            $figures[$session->id]->amount = '';
            if($session->enabled($fund))
            {
                $figures[$session->id]->count =
                    $this->getRemittanceCount($sessionCount, $subscriptionCount, $rank++);
                $figures[$session->id]->amount = $formattedAmount;
            }
        }

        return $sessionId > 0 ? $figures[$sessionId] : $figures;
    }
}
