<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\MeetingComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Ajax\App\Meeting\Session\Pool\Remitment;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;

use function Jaxon\pm;
use function trans;

/**
 * @before getPool
 */
class Pool extends MeetingComponent
{
    use PoolTrait;

    /**
     * @var string
     */
    protected $overrides = Remitment::class;

    /**
     * @var BalanceCalculator
     */
    protected BalanceCalculator $balanceCalculator;

    /**
     * @var RemitmentValidator
     */
    protected RemitmentValidator $validator;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param RemitmentService $remitmentService
     */
    public function __construct(protected PoolService $poolService,
        protected RemitmentService $remitmentService)
    {}

    /**
     * @di $balanceCalculator
     */
    public function pool(int $poolId)
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');

        return $this->renderView('pages.meeting.remitment.pool.home', [
            'pool' => $pool,
            'depositAmount' => $this->balanceCalculator->getPoolDepositAmount($pool, $session),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PoolPage::class)->render();
    }

    public function createRemitment(int $payableId)
    {
        $pool = $this->cache->get('meeting.pool');
        if(!$pool->remit_planned || $pool->remit_auction)
        {
            // Only when remitments are planned and without auctions.
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $this->remitmentService->savePlannedRemitment($pool, $session, $payableId);

        return $this->cl(PoolPage::class)->render();
    }

    public function addRemitment(int $payableId)
    {
        // if($pool->remit_planned && !$pool->remit_auction)
        // {
        //     // Only when remitments are not planned or with auctions.
        //     return $this->response;
        // }

        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');

        $title = trans('meeting.remitment.titles.add');
        $content = $this->renderView('pages.meeting.remitment.pool.add', [
            'pool' => $pool,
            'payableId' => $payableId,
            'members' => $this->remitmentService->getSubscriptions($pool, $session),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRemitment(pm()->form('remitment-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveRemitment(array $formValues)
    {
        $pool = $this->cache->get('meeting.pool');
        // if($pool->remit_planned && !$pool->remit_auction)
        // {
        //     // Only when remitments are not planned or with auctions.
        //     $this->dialog->hide();
        //     return $this->response;
        // }

        // Add some data in the input values to help validation.
        $formValues['remit_auction'] = $pool->remit_auction ? 1 : 0;

        $session = $this->cache->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->remitmentService->saveRemitment($pool, $session,
            $values['payable'], $values['auction']);
        $this->dialog->hide();

        return $this->cl(PoolPage::class)->render();
    }

    /**
     * @param int $payableId
     */
    public function deleteRemitment(int $payableId)
    {
        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');
        $this->remitmentService->deleteRemitment($pool, $session, $payableId);

        return $this->cl(PoolPage::class)->render();
    }
}
