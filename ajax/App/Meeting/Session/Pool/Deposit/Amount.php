<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\MeetingComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Jaxon\Response\AjaxResponse;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\PaymentServiceInterface;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Stringable;

use function filter_var;
use function str_replace;
use function trans;
use function trim;

/**
 * @before getPool
 */
class Amount extends MeetingComponent
{
    use PoolTrait;
    use DepositTrait;

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param PoolService $poolService
     * @param DepositService $depositService
     */
    public function __construct(private LocaleService $localeService,
        private PoolService $poolService, private DepositService $depositService,
        private PaymentServiceInterface $paymentService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $session = $this->stash()->get('meeting.session');
        $receivable = $this->stash()->get('meeting.session.receivable');

        if($session->closed)
        {
            return $this->renderView('pages.meeting.deposit.libre.closed', [
                'amount' => !$receivable->deposit ? '' :
                    $this->localeService->formatMoney($receivable->deposit->amount, true),
            ]);
        }

        // When editing the deposit amount, or when there is no deposit yet,
        // then show the amount edit form.
        $edit = $this->stash()->get('meeting.session.edit');
        if($edit || !$receivable->deposit)
        {
            return $this->renderView('pages.meeting.deposit.libre.edit', [
                'receivableId' => $receivable->id,
                'amount' => !$receivable->deposit ? '' :
                    $this->localeService->getMoneyValue($receivable->deposit->amount),
                'rqAmount' => $this->rq(),
            ]);
        }

        return $this->renderView('pages.meeting.deposit.libre.show', [
            'receivableId' => $receivable->id,
            'amount' => $this->localeService->formatMoney($receivable->deposit->amount, false),
            'editable' => $this->paymentService->isEditable($receivable->deposit),
            'rqAmount' => $this->rq(),
        ]);
    }

    /**
     * @param int $receivableId
     *
     * @return AjaxResponse
     */
    public function edit(int $receivableId): AjaxResponse
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $receivable = $this->depositService->getReceivable($pool, $session, $receivableId);
        if(!$receivable || !$receivable->deposit)
        {
            return $this->cl(ReceivablePage::class)->page();
        }

        $this->stash()->set('meeting.session.edit', true);
        $this->stash()->set('meeting.session.receivable', $receivable);

        return $this->item($receivable->id)->render();
    }

    /**
     * @param int $receivableId
     * @param string $amount
     *
     * @return AjaxResponse
     */
    public function save(int $receivableId, string $amount): AjaxResponse
    {
        $amount = str_replace(',', '.', trim($amount));
        if($amount !== '' && filter_var($amount, FILTER_VALIDATE_FLOAT) === false)
        {
            $error = trans('meeting.errors.amount.invalid', ['amount' => $amount]);
            $this->alert()->title(trans('common.titles.error'))->error($error);

            return $this->response;
        }

        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $amount = $amount === '' ? 0 : $this->localeService->convertMoneyToInt((float)$amount);
        $amount > 0 ?
            $this->depositService->saveDepositAmount($pool, $session, $receivableId, $amount):
            $this->depositService->deleteDeposit($pool, $session, $receivableId);

        $this->stash()->set('meeting.session.receivable',
            $this->depositService->getReceivable($pool, $session, $receivableId));

        $this->showTotal();
        return $this->item($receivableId)->render();
    }
}
