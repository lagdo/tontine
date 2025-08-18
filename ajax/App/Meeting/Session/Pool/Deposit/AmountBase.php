<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\Session\FuncComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Model\Receivable as ReceivableModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;

use function filter_var;
use function str_replace;
use function trans;
use function trim;

/**
 * @before getPool
 */
abstract class AmountBase extends FuncComponent
{
    use PoolTrait;

    /**
     * @var string
     */
    protected string $amountClass = Amount::class;

    /**
     * @var string
     */
    protected string $receivablePageClass = ReceivablePage::class;

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
     * @return void
     */
    abstract protected function showTotal(): void;

    /**
     * @param int $receivableId
     *
     * @return ReceivableModel|null
     */
    abstract protected function getReceivable(int $receivableId): ?ReceivableModel;

    /**
     * @param int $receivableId
     * @param int $amount
     *
     * @return void
     */
    abstract protected function saveDeposit(int $receivableId, int $amount): void;

    /**
     * @param int $receivableId
     *
     * @return void
     */
    public function edit(int $receivableId): void
    {
        $receivable = $this->getReceivable($receivableId);
        if(!$receivable || !$receivable->deposit)
        {
            $this->cl($this->receivablePageClass)->page();
            return;
        }

        $this->stash()->set('meeting.session.edit', true);
        $this->stash()->set('meeting.session.receivable', $receivable);

        $this->cl($this->amountClass)->item($receivable->id)->render();
    }

    /**
     * @param int $receivableId
     * @param string $amount
     *
     * @return void
     */
    public function save(int $receivableId, string $amount): void
    {
        $amount = str_replace(',', '.', trim($amount));
        if($amount !== '' && filter_var($amount, FILTER_VALIDATE_FLOAT) === false)
        {
            $error = trans('meeting.errors.amount.invalid', ['amount' => $amount]);
            $this->alert()->title(trans('common.titles.error'))->error($error);
            return;
        }

        $this->saveDeposit($receivableId, $amount === '' ? 0 :
            $this->localeService->convertMoneyToInt((float)$amount));
        $this->stash()->set('meeting.session.receivable',
            $this->getReceivable($receivableId));

        $this->showTotal();
        $this->cl($this->amountClass)->item($receivableId)->render();
    }
}
