<?php

namespace App\Ajax\Web\Meeting\Session;

use App\Ajax\Cache;
use App\Ajax\CallableClass;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;

use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Misc extends CallableClass
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @param SessionService $sessionService
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(private SessionService $sessionService,
        private BalanceCalculator $balanceCalculator)
    {}

    /**
     * @return int
     */
    protected function getSessionId(): int
    {
        return (int)$this->bag('meeting')->get('session.id');
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $session = $this->sessionService->getSession($this->getSessionId());
        if($session === null)
        {
            throw new MessageException(trans('meeting.errors.session.not_found'));
        }
        Cache::set('meeting.session', $session);
    }

    /**
     * @di $localeService
     */
    public function showBalanceAmounts()
    {
        $session = Cache::get('meeting.session');
        $amount = $this->balanceCalculator->getBalanceForLoan($session);
        $html = trans('meeting.loan.labels.amount_available', [
            'amount' => $this->localeService->formatMoney($amount),
        ]);
        $this->response->html('loan_amount_available', $html);

        $amount = $this->balanceCalculator->getTotalBalance($session);
        $html = trans('meeting.disbursement.labels.amount_available', [
            'amount' => $this->localeService->formatMoney($amount),
        ]);
        $this->response->html('total_amount_available', $html);

        return $this->response;
    }

    public function showBalanceDetails(bool $lendable)
    {
        $session = Cache::get('meeting.session');
        $balances = $this->balanceCalculator->getBalances($session, $lendable);
        $title = trans('meeting.titles.amounts');
        $content = $this->renderView('pages.meeting.session.balances', [
            'session' => $session,
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
        $session = Cache::get('meeting.session');
        $this->sessionService->saveAgenda($session, $text);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.agenda.updated'));

        return $this->response;
    }

    public function saveReport(string $text)
    {
        $session = Cache::get('meeting.session');
        $this->sessionService->saveReport($session, $text);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.report.updated'));

        return $this->response;
    }
}
