<?php

namespace App\Ajax\Web\Meeting;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Balance extends CallableClass
{
    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @param LocaleService $localeService
     * @param SessionService $sessionService
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(private LocaleService $localeService,
        private SessionService $sessionService, private BalanceCalculator $balanceCalculator)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->sessionService->getSession($sessionId);
    }

    public function showAmounts()
    {
        $amount = $this->balanceCalculator->getBalanceForLoan($this->session);
        $html = trans('meeting.loan.labels.amount_available', [
            'amount' => $this->localeService->formatMoney($amount),
        ]);
        $this->response->html('loan_amount_available', $html);

        $amount = $this->balanceCalculator->getTotalBalance($this->session);
        $html = trans('meeting.disbursement.labels.amount_available', [
            'amount' => $this->localeService->formatMoney($amount),
        ]);
        $this->response->html('total_amount_available', $html);

        return $this->response;
    }

    public function show(bool $lendable)
    {
        if(!$this->session)
        {
            return $this->response;
        }

        $balances = $this->balanceCalculator->getBalances($this->session, $lendable);
        $title = trans('meeting.titles.amounts');
        $content = $this->render('pages.meeting.session.balances', [
            'session' => $this->session,
            'balances' => $balances,
        ]);
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }
}
