<?php

namespace App\Ajax\Web\Meeting\Session;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;

use function trans;

class Session extends CallableSessionClass
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(private BalanceCalculator $balanceCalculator)
    {}

    /**
     * @di $localeService
     */
    public function showBalanceAmounts()
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

    public function showBalanceDetails(bool $lendable)
    {
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

    public function open()
    {
        $this->sessionService->openSession($this->session);
        $this->cl(Home::class)->show($this->session);

        return $this->response;
    }

    public function close()
    {
        $this->sessionService->closeSession($this->session);
        $this->cl(Home::class)->show($this->session);

        return $this->response;
    }

    public function saveAgenda(string $text)
    {
        $this->sessionService->saveAgenda($this->session, $text);
        $this->notify->success(trans('meeting.messages.agenda.updated'), trans('common.titles.success'));

        return $this->response;
    }

    public function saveReport(string $text)
    {
        $this->sessionService->saveReport($this->session, $text);
        $this->notify->success(trans('meeting.messages.report.updated'), trans('common.titles.success'));

        return $this->response;
    }
}
