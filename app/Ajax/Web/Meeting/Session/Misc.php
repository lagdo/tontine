<?php

namespace App\Ajax\Web\Meeting\Session;

use App\Ajax\SessionCallable;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;

use function trans;

class Misc extends SessionCallable
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
        $content = $this->renderView('pages.meeting.session.balances', [
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

    public function saveAgenda(string $text)
    {
        $this->sessionService->saveAgenda($this->session, $text);
        $this->notify->title(trans('common.titles.success'))->success(trans('meeting.messages.agenda.updated'));

        return $this->response;
    }

    public function saveReport(string $text)
    {
        $this->sessionService->saveReport($this->session, $text);
        $this->notify->title(trans('common.titles.success'))->success(trans('meeting.messages.report.updated'));

        return $this->response;
    }
}
