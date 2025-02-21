<?php

namespace Ajax\App\Meeting\Session\Saving;

use Ajax\App\Meeting\FuncComponent;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\ClosingValidator;

use function Jaxon\pm;
use function trans;

/**
 * @databag report
 */
class ClosingFunc extends FuncComponent
{
    /**
     * @var ClosingValidator
     */
    protected ClosingValidator $validator;

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param ClosingService $closingService
     */
    public function __construct(protected FundService $fundService,
        protected ClosingService $closingService)
    {}

    public function editRoundClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.closing.titles.round');
        $content = $this->renderView('pages.meeting.closing.round', [
            'fund' => $fund,
            'closing' => $this->closingService->getRoundClosing($session, $fund),
        ]);
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ], [
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRoundClosing($fundId, pm()->form('closing-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function saveRoundClosing(int $fundId, array $formValues)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->closingService->saveRoundClosing($session, $fund, $values['amount']);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.profit.saved'));

        $this->cl(Closing::class)->render();
    }

    public function deleteRoundClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->closingService->deleteRoundClosing($session, $fund);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.profit.deleted'));

        $this->cl(Closing::class)->render();
    }

    public function editInterestClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $title = trans('meeting.closing.titles.interest');
        $content = $this->renderView('pages.meeting.closing.interest', [
            'fund' => $fund,
            'closing' => $this->closingService->getInterestClosing($session, $fund),
        ]);
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ], [
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveInterestClosing($fundId),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function saveInterestClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->closingService->saveInterestClosing($session, $fund);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.profit.saved'));

        $this->cl(Closing::class)->render();
    }

    public function deleteInterestClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->closingService->deleteInterestClosing($session, $fund);

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('meeting.messages.profit.deleted'));

        $this->cl(Closing::class)->render();
    }
}
