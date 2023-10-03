<?php

namespace App\Ajax\Web\Meeting\Pool\Remitment;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Cash\Disbursement;
use App\Ajax\Web\Meeting\Credit\Loan;
use App\Ajax\Web\Meeting\Pool\Auction;
use App\Ajax\Web\Meeting\Pool\Remitment;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getPool
 */
class Pool extends CallableClass
{
    /**
     * @var RemitmentService
     */
    protected RemitmentService $remitmentService;

    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @var BalanceCalculator
     */
    protected BalanceCalculator $balanceCalculator;

    /**
     * @var RemitmentValidator
     */
    protected RemitmentValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param RemitmentService $remitmentService
     */
    public function __construct(PoolService $poolService, RemitmentService $remitmentService)
    {
        $this->poolService = $poolService;
        $this->remitmentService = $remitmentService;
    }

    /**
     * @return void
     */
    protected function getPool()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->remitmentService->getSession($sessionId);

        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id');
        $this->pool = $this->remitmentService->getPool($poolId);

        if(!$this->session || !$this->pool || $this->session->disabled($this->pool))
        {
            $this->notify->error(trans('tontine.session.errors.disabled'), trans('common.titles.error'));
            $this->pool = null;
        }
    }

    /**
     * @di $balanceCalculator
     */
    public function home(int $poolId)
    {
        $this->bag('meeting')->set('pool.id', $poolId);

        $html = $this->view()->render('tontine.pages.meeting.remitment.pool.home', [
            'pool' => $this->pool,
            'depositAmount' => $this->balanceCalculator->getPoolDepositAmount($this->pool, $this->session),
        ]);
        $this->response->html('meeting-remitments', $html);

        if(!$this->pool->remit_planned)
        {
            $this->jq('#btn-new-remitment')->click($this->rq()->addRemitment(0));
        }
        $this->jq('#btn-remitments-back')->click($this->cl(Remitment::class)->rq()->home());

        return $this->page();
    }

    public function page()
    {
        $html = $this->view()->render('tontine.pages.meeting.remitment.pool.page', [
            'pool' => $this->pool,
            'session' => $this->session,
            'payables' => $this->remitmentService->getPayables($this->pool, $this->session),
        ]);
        $this->response->html('meeting-pool-remitments', $html);

        $payableId = jq()->parent()->attr('data-payable-id')->toInt();
        $this->jq('.btn-new-remitment')->click($this->rq()->addRemitment($payableId));
        $this->jq('.btn-add-remitment')->click($this->rq()->createRemitment($payableId));
        $this->jq('.btn-del-remitment')->click($this->rq()->deleteRemitment($payableId));

        return $this->response;
    }

    public function createRemitment(int $payableId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        if(!$this->pool->remit_planned || $this->pool->remit_auction)
        {
            // Only when remitments are planned and without auctions.
            return $this->response;
        }

        $this->remitmentService->savePlannedRemitment($this->pool, $this->session, $payableId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }

    public function addRemitment(int $payableId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        // if($this->pool->remit_planned && !$this->pool->remit_auction)
        // {
        //     // Only when remitments are not planned or with auctions.
        //     return $this->response;
        // }

        $members = $this->remitmentService->getSubscriptions($this->pool, $this->session);
        $title = trans('meeting.remitment.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.remitment.pool.add')
            ->with('pool', $this->pool)
            ->with('payableId', $payableId)
            ->with('members', $members);
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
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            $this->dialog->hide();
            return $this->response;
        }
        // if($this->pool->remit_planned && !$this->pool->remit_auction)
        // {
        //     // Only when remitments are not planned or with auctions.
        //     $this->dialog->hide();
        //     return $this->response;
        // }

        // Add some data in the input values to help validation.
        $formValues['remit_amount'] = $this->pool->remit_fixed ? 0 : 1;
        $formValues['remit_auction'] = $this->pool->remit_auction ? 1 : 0;

        $values = $this->validator->validateItem($formValues);
        $this->remitmentService->saveRemitment($this->pool, $this->session,
            $values['payable'], $values['amount'], $values['auction']);
        $this->dialog->hide();

        // Refresh the auction page
        if($this->pool->remit_auction)
        {
            $this->cl(Auction::class)->show($this->session);
        }
        // Refresh the refunds pages
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }

    /**
     * @param int $payableId
     */
    public function deleteRemitment(int $payableId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->remitmentService->deleteRemitment($this->pool, $this->session, $payableId);

        // Refresh the auction page
        if($this->pool->remit_auction)
        {
            $this->cl(Auction::class)->show($this->session);
        }
        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }
}
