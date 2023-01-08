<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Pool;
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

        $figures->remitment = new stdClass();
        $figures->remitment->count = $defaultValue;
        $figures->remitment->amount = $defaultValue;

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
        $figures->remitment->amount = Currency::format($figures->remitment->amount, true);

        return $figures;
    }

    /**
     * @param Pool $pool
     * @param Collection $sessions
     * @param Collection $subscriptions
     *
     * @return array
     */
    private function getExpectedFigures(Pool $pool, Collection $sessions, Collection $subscriptions): array
    {
        $sessionCount = $sessions->filter(function($session) use($pool) {
            return $session->enabled($pool);
        })->count();
        $subscriptionCount = $pool->subscriptions()->count();
        $depositCount = $subscriptions->count();

        $remitmentAmount = $pool->amount * $sessionCount;
        $depositAmount = $pool->amount * $subscriptions->count();

        $rank = 0;
        $cashier = 0;
        $expectedFigures = [];
        foreach($sessions as $session)
        {
            if($session->disabled($pool))
            {
                $expectedFigures[$session->id] = $this->makeFigures('');
                continue;
            }

            $figures = $this->makeFigures(0);

            $figures->cashier->start = $cashier;
            $figures->cashier->recv = $cashier + $depositAmount;
            $figures->deposit->count = $depositCount;
            $figures->deposit->amount = $depositAmount;
            $figures->remitment->count =
                $this->getRemitmentCount($sessionCount, $subscriptionCount, $rank++);
            $figures->remitment->amount = $remitmentAmount * $figures->remitment->count;
            $figures->cashier->end = $cashier + $depositAmount - $figures->remitment->amount;
            $cashier = $figures->cashier->end;

            $expectedFigures[$session->id] = $this->formatCurrencies($figures);
        }

        return $expectedFigures;
    }

    /**
     * @param Pool $pool
     * @param Collection $sessions
     * @param Collection $subscriptions
     *
     * @return array
     */
    private function getCollectedFigures(Pool $pool, Collection $sessions, Collection $subscriptions): array
    {
        $cashier = 0;
        $remitmentAmount = $pool->amount * $sessions->filter(function($session) use($pool) {
            return $session->enabled($pool);
        })->count();

        $collectedFigures = [];
        foreach($sessions as $session)
        {
            if($session->disabled($pool) || $session->pending)
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
                    $figures->deposit->amount += $pool->amount;
                    $figures->cashier->recv += $pool->amount;
                }
            }
            $figures->cashier->end = $figures->cashier->recv;
            foreach($session->payables as $payable)
            {
                if(($payable->remitment))
                {
                    $figures->remitment->count++;
                    $figures->remitment->amount += $remitmentAmount;
                    $figures->cashier->end -= $remitmentAmount;
                }
            }

            $cashier = $figures->cashier->end;
            $collectedFigures[$session->id] = $this->formatCurrencies($figures);
        }

        return $collectedFigures;
    }

    /**
     * Get the receivables of a given pool.
     *
     * Will return basic data on subscriptions.
     *
     * @param Pool $pool
     *
     * @return array
     */
    public function getReceivables(Pool $pool): array
    {
        $sessions = $this->tenantService->round()->sessions()->get();
        $subscriptions = $pool->subscriptions()->with(['member'])->get();
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($pool, $sessions, $subscriptions);

        return compact('pool', 'sessions', 'subscriptions', 'figures');
    }

    /**
     * Get the payables of a given pool.
     *
     * @param Pool $pool
     * @param array $with
     *
     * @return Collection
     */
    private function _getSessions(Pool $pool, array $with = []): Collection
    {
        $with['payables'] = function($query) use($pool) {
            // Keep only the subscriptions of the current pool.
            $query->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
                ->where('subscriptions.pool_id', $pool->id);
        };
        return $this->tenantService->round()->sessions()->with($with)->get();
    }

    /**
     * Get the payables of a given pool.
     *
     * @param Pool $pool
     *
     * @return array
     */
    public function getPayables(Pool $pool): array
    {
        $sessions = $this->_getSessions($pool, ['payables.subscription']);
        $subscriptions = $pool->subscriptions()->with(['payable', 'member'])->get();
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($pool, $sessions, $subscriptions);

        // Set the subscriptions that will be pay at each session.
        // Pad with 0's when the beneficiaries are not yet set.
        $sessions->each(function($session) use($figures, $pool) {
            if($session->disabled($pool))
            {
                return;
            }
            // Pick the subscriptions ids, and fill with 0's to the max available.
            $session->beneficiaries = $session->payables->map(function($payable) {
                return $payable->subscription_id;
            })->pad($figures->expected[$session->id]->remitment->count, 0);
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

        return compact('pool', 'sessions', 'subscriptions', 'beneficiaries', 'figures');
    }

    /**
     * Get the receivables of a given pool.
     *
     * Will return extended data on subscriptions.
     *
     * @param Pool $pool
     *
     * @return array
     */
    public function getFigures(Pool $pool): array
    {
        $subscriptions = $pool->subscriptions()->with(['member', 'receivables.deposit'])
            ->get()->each(function($subscription) {
                $subscription->setRelation('receivables', $subscription->receivables->keyBy('session_id'));
            });
        $sessions = $this->_getSessions($pool, ['payables.remitment']);
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($pool, $sessions, $subscriptions);
        $figures->collected = $this->getCollectedFigures($pool, $sessions, $subscriptions);

        return compact('pool', 'sessions', 'subscriptions', 'figures');
    }

    /**
     * Get the number of subscribers to remit a pool to at a given session
     *
     * @param int $sessionCount
     * @param int $subscriptionCount
     * @param int $sessionRank
     *
     * @return int
     */
    public function getRemitmentCount(int $sessionCount, int $subscriptionCount, int $sessionRank): int
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
     * @param Pool $pool
     * @param int $sessionId
     *
     * @return array|stdClass
     */
    public function getRemitmentFigures(Pool $pool, int $sessionId = 0)
    {
        $sessions = $this->_getSessions($pool, ['payables.subscription.member']);
        $sessionCount = $sessions->filter(function($session) use($pool) {
            return $session->enabled($pool);
        })->count();
        $subscriptionCount = $pool->subscriptions()->count();
        $remitmentAmount = $pool->amount * $sessionCount;
        $formattedAmount = Currency::format($remitmentAmount);

        $figures = [];
        $rank = 0;
        foreach($sessions as $session)
        {
            $figures[$session->id] = new stdClass();
            $figures[$session->id]->payables = $session->payables;
            $figures[$session->id]->count = 0;
            $figures[$session->id]->amount = '';
            if($session->enabled($pool))
            {
                $figures[$session->id]->count =
                    $this->getRemitmentCount($sessionCount, $subscriptionCount, $rank++);
                $figures[$session->id]->amount = $formattedAmount;
            }
        }

        return $sessionId > 0 ? $figures[$sessionId] : $figures;
    }
}
