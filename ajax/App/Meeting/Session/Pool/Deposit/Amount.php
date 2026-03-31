<?php

namespace Ajax\App\Meeting\Session\Pool\Deposit;

use Ajax\App\Meeting\Session\Component;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;

#[Before('getPool')]
class Amount extends Component
{
    use PoolTrait;

    /**
     * @var string
     */
    protected string $amountFuncClass = AmountFunc::class;

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
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->stash()->get('meeting.session');
        $receivable = $this->stash()->get('meeting.session.receivable');

        if($session->closed)
        {
            return $this->renderTpl('pages.meeting.session.deposit.libre.closed', [
                'amount' => !$receivable->deposit ? '' :
                    $this->localeService->formatMoney($receivable->deposit->amount),
            ]);
        }

        // When editing the deposit amount, or when there is no deposit yet,
        // then show the amount edit form.
        $edit = $this->stash()->get('meeting.session.edit');
        if($edit || !$receivable->deposit)
        {
            return $this->renderTpl('pages.meeting.session.deposit.libre.edit', [
                'receivableId' => $receivable->id,
                'amount' => !$receivable->deposit ? '' :
                    $this->localeService->getMoneyValue($receivable->deposit->amount),
                'rqAmountFunc' => $this->rq($this->amountFuncClass),
            ]);
        }

        return $this->renderTpl('pages.meeting.session.deposit.libre.show', [
            'receivableId' => $receivable->id,
            'amount' => $this->localeService->formatMoney($receivable->deposit->amount, false),
            'editable' => $this->paymentService->isEditable($receivable->deposit),
            'rqAmountFunc' => $this->rq($this->amountFuncClass),
        ]);
    }
}
