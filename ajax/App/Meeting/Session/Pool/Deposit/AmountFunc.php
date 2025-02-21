<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\FuncComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\PaymentServiceInterface;

use function filter_var;
use function str_replace;
use function trans;
use function trim;

/**
 * @before getPool
 */
class AmountFunc extends FuncComponent
{
    use PoolTrait;
    use DepositTrait;

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param PaymentServiceInterface $paymentService
     */
    public function __construct(private LocaleService $localeService,
        private PaymentServiceInterface $paymentService)
    {}

    /**
     * @param int $receivableId
     *
     * @return void
     */
    public function edit(int $receivableId)
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $receivable = $this->depositService->getReceivable($pool, $session, $receivableId);
        if(!$receivable || !$receivable->deposit)
        {
            $this->cl(ReceivablePage::class)->page();
            return;
        }

        $this->stash()->set('meeting.session.edit', true);
        $this->stash()->set('meeting.session.receivable', $receivable);

        $this->cl(Amount::class)->item($receivable->id)->render();
    }

    /**
     * @param int $receivableId
     * @param string $amount
     *
     * @return void
     */
    public function save(int $receivableId, string $amount)
    {
        $amount = str_replace(',', '.', trim($amount));
        if($amount !== '' && filter_var($amount, FILTER_VALIDATE_FLOAT) === false)
        {
            $error = trans('meeting.errors.amount.invalid', ['amount' => $amount]);
            $this->alert()->title(trans('common.titles.error'))->error($error);

            return;
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
        $this->cl(Amount::class)->item($receivableId)->render();
    }
}
