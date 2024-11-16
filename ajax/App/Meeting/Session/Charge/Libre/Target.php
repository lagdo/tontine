<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\ChargeComponent;
use Ajax\App\Meeting\Session\Charge\LibreFee;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;
use Siak\Tontine\Validation\Meeting\TargetValidator;

use function Jaxon\pm;
use function trans;
use function trim;

/**
 * @before getTarget
 */
class Target extends ChargeComponent
{
    /**
     * @var string
     */
    protected $overrides = LibreFee::class;

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TargetValidator
     */
    protected TargetValidator $validator;

    /**
     * The constructor
     *
     * @param SettlementTargetService $targetService
     */
    public function __construct(protected SettlementTargetService $targetService)
    {}

    protected function getTarget()
    {
        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $target = $session !== null && $charge !== null ?
            $this->targetService->getTarget($charge, $session) : null;
        $this->cache->set('meeting.session.charge.target', $target);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return $this->renderView('pages.meeting.charge.libre.target.home', [
            'charge' => $this->cache->get('meeting.session.charge'),
            'target' => $this->cache->get('meeting.session.charge.target'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(TargetPage::class)->page();
    }

    /**
     * @param int $chargeId
     *
     * @return mixed
     */
    public function charge(int $chargeId)
    {
        $this->bag('meeting')->set('fee.member.search', '');
        $this->bag('meeting')->set('fee.target.page', 1);

        return $this->render();
    }

    public function search(string $search)
    {
        $this->bag('meeting')->set('fee.member.search', trim($search));

        return $this->cl(TargetPage::class)->page();
    }

    /**
     * @before checkChargeEdit
     * @return mixed
     */
    public function add()
    {
        $target = $this->cache->get('meeting.session.charge.target');
        if($target !== null)
        {
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $title = trans('meeting.target.titles.set');
        $content = $this->renderView('pages.meeting.charge.libre.target.add', [
            'sessions' => $this->targetService->getDeadlineSessions($session),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('target-form')),
        ]];

        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     * @before checkChargeEdit
     * @param array $formValues
     *
     * @return mixed
     */
    public function create(array $formValues)
    {
        $target = $this->cache->get('meeting.session.charge.target');
        if($target !== null)
        {
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $formValues['global'] = isset($formValues['global']);
        $values = $this->validator->validateItem($formValues);
        $deadlineSession = $this->sessionService->getTontineSession($values['deadline']);

        $this->targetService->createTarget($charge, $session,
            $deadlineSession, $values['amount'], $values['global']);
        $this->dialog->hide();

        $this->cache->set('meeting.session.charge.target',
            $this->targetService->getTarget($charge, $session));
        return $this->charge($charge->id);
    }

    /**
     * @before checkChargeEdit
     * @return mixed
     */
    public function edit()
    {
        $target = $this->cache->get('meeting.session.charge.target');
        if($target === null)
        {
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $title = trans('meeting.target.titles.set');
        $content = $this->renderView('pages.meeting.charge.libre.target.edit', [
            'target' => $target,
            'sessions' => $this->targetService->getDeadlineSessions($session),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update(pm()->form('target-form')),
        ]];

        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     * @before checkChargeEdit
     * @param int $memberId
     * @param string $amount
     *
     * @return mixed
     */
    public function update(array $formValues)
    {
        $target = $this->cache->get('meeting.session.charge.target');
        if($target === null)
        {
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $formValues['global'] = isset($formValues['global']);
        $values = $this->validator->validateItem($formValues);
        $deadlineSession = $this->sessionService->getTontineSession($values['deadline']);

        $this->targetService->updateTarget($target, $session,
            $deadlineSession, $values['amount'], $values['global']);
        $this->dialog->hide();

        $this->cache->set('meeting.session.charge.target',
            $this->targetService->getTarget($charge, $session));
        return $this->charge($charge->id);
    }

    /**
     * @before checkChargeEdit
     * @return mixed
     */
    public function remove()
    {
        $target = $this->cache->get('meeting.session.charge.target');
        if($target === null)
        {
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $charge = $this->cache->get('meeting.session.charge');
        $this->targetService->deleteTarget($target);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('meeting.target.messages.removed'));

        $this->cache->set('meeting.session.charge.target',
            $this->targetService->getTarget($charge, $session));
        return $this->charge($charge->id);
    }
}