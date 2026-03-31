<?php

namespace Ajax\App\Planning\Pool\Subscription;

use Ajax\App\Planning\Pool\PoolTrait;
use Ajax\App\Planning\Component;
use Ajax\Page\SectionContent;
use Illuminate\Support\Collection;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Export;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService;

use function array_values;
use function collect;
use function compact;
use function Jaxon\jq;
use function trans;

#[Before('getPool')]
#[Databag('planning.pool')]
#[Export(base: ['render'])]
class Beneficiary extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected string $overrides = SectionContent::class;

    /**
     * @var SubscriptionService
     */
    protected SubscriptionService $subscriptionService;

    /**
     * @var array
     */
    protected array $payables;

    /**
     * @var Collection
     */
    protected Collection $candidates;

    /**
     * @var Collection
     */
    protected Collection $beneficiaries;

    /**
     * The constructor
     *
     * @param SummaryService $summaryService
     */
    public function __construct(private SummaryService $summaryService)
    {}

    public function pool(int $poolId)
    {
        $this->render();
    }

    /**
     * @inheritDoc
     */
    protected function before(): void
    {
        $pool = $this->stash()->get('planning.pool');
        if(!$pool->remit_planned)
        {
            throw new MessageException(trans('tontine.pool.errors.not_planned'));
        }
    }

    /**
     * @param bool $remitAuction
     *
     * @return Collection
     */
    private function getCandidates(bool $remitAuction): Collection
    {
        return $remitAuction ? collect() : $this->payables['subscriptions']
            ->filter(fn($subscription) => $subscription->payable->session === null)
            ->pluck('member.name', 'id')->sort()->prepend('', 0);
    }

    /**
     * @return Collection
     */
    private function getBeneficiaries(): Collection
    {
        return $this->payables['subscriptions']
            ->filter(fn($subscription) => $subscription->payable->session !== null)
            ->pluck('member.name', 'id');
    }

    /**
     * @param SessionModel $session
     *
     * @return array
     */
    private function getSession(SessionModel $session): array
    {
        $figures = $this->payables['figures']->expected[$session->id];
        return [
            'id' => $session->id,
            'title' => $session->title,
            'figures' => $figures,
            'payables' => $session->payables
                ->map(fn($payable) => $payable->subscription_id)
                ->pad($figures->remitment->count, 0),
            'beneficiaries' => $session->payables->keyBy('subscription_id')
                ->map(fn($payable) => $payable->remitment !== null ?
                    // If the remitment exists, then return the beneficiary name.
                    $this->beneficiaries[$payable->subscription_id] : null),
        ];
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->stash()->get('planning.pool');
        $this->payables = $this->summaryService->getPayables($pool);
        // Subscriptions that don't have a beneficiary assigned.
        $this->candidates = $this->getCandidates($pool->remit_auction);
        // Subscriptions that already have a beneficiary assigned.
        $this->beneficiaries = $this->getBeneficiaries();

        return $this->renderTpl('pages.planning.pool.subscription.beneficiaries', [
            'pool' => $pool,
            'candidates' => $this->candidates,
            'sessions' => $this->payables['sessions']
                ->map(fn($session) => $this->getSession($session))
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after(): void
    {
        $callback = fn(string $name, int $id) => compact('id', 'name');
        $candidates = array_values($this->candidates->map($callback)->all());
        $beneficiaries = array_values($this->beneficiaries->map($callback)->all());
        $this->response()->jo('tontine')
            ->setSubscriptionCandidates($candidates, $beneficiaries);

        $sessionId = jq()->parent()->parent()->attr('data-session-id')->toInt();
        $nextSubscriptionId = jq()->val()->toInt();
        $currSubscriptionId = jq()->parent()->attr('data-subscription-id')->toInt();
        $wrapper = '#content-subscription-beneficiaries';
        $this->response()->jq('.select-beneficiary', $wrapper)
            ->on('change', $this->rq()->save($sessionId,
                $nextSubscriptionId, $currSubscriptionId));

        $this->response()->jo('tontine')
            ->makeTableResponsive('content-subscription-beneficiaries');
    }

    /**
     * @param int $sessionId
     * @param int $nextSubscriptionId
     * @param int $currSubscriptionId
     *
     * @return void
     */
    #[Inject(attr: 'subscriptionService')]
    public function save(int $sessionId, int $nextSubscriptionId, int $currSubscriptionId)
    {
        $pool = $this->stash()->get('planning.pool');
        if(!$pool || !$pool->remit_planned || $pool->remit_auction)
        {
            return;
        }

        if(!$this->subscriptionService->saveBeneficiary($pool, $sessionId,
            $currSubscriptionId, $nextSubscriptionId))
        {
            $message = trans('tontine.beneficiary.errors.cant_change');
            $this->alert()->title(trans('common.titles.error'))
                ->error($message);
        }

        $this->render();
    }
}
