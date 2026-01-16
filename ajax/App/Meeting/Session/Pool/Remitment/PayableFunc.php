<?php

namespace Ajax\App\Meeting\Session\Pool\Remitment;

use Ajax\App\Meeting\Session\FuncComponent;
use Ajax\App\Meeting\Session\Pool\PoolTrait;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;

use function je;
use function trans;

#[Before('getPool')]
class PayableFunc extends FuncComponent
{
    use PoolTrait;

    /**
     * @var RemitmentValidator
     */
    protected RemitmentValidator $validator;

    /**
     * The constructor
     *
     * @param RemitmentService $remitmentService
     */
    public function __construct(protected RemitmentService $remitmentService)
    {}

    public function createRemitment(int $payableId): void
    {
        $pool = $this->stash()->get('meeting.pool');
        if(!$pool->remit_planned || $pool->remit_auction)
        {
            // Only when remitments are planned and without auctions.
            return;
        }

        $session = $this->stash()->get('meeting.session');
        $this->remitmentService->savePlannedRemitment($pool, $session, $payableId);

        $this->cl(Total::class)->render();
        $this->cl(PayablePage::class)->render();
    }

    public function addRemitment(int $payableId): void
    {
        // if($pool->remit_planned && !$pool->remit_auction)
        // {
        //     // Only when remitments are not planned or with auctions.
        //     return;
        // }

        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');

        $title = trans('meeting.remitment.titles.add');
        $content = $this->renderTpl('pages.meeting.session.remitment.payable.add', [
            'pool' => $pool,
            'payableId' => $payableId,
            'members' => $this->remitmentService->getSubscriptions($pool, $session),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRemitment(je('remitment-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function saveRemitment(array $formValues): void
    {
        $pool = $this->stash()->get('meeting.pool');
        // if($pool->remit_planned && !$pool->remit_auction)
        // {
        //     // Only when remitments are not planned or with auctions.
        //     $this->modal()->hide();
        //     return;
        // }

        // Add some data in the input values to help validation.
        $formValues['remit_auction'] = $pool->remit_auction ? 1 : 0;

        $session = $this->stash()->get('meeting.session');
        $values = $this->validator->validateItem($formValues);
        $this->remitmentService->saveRemitment($pool, $session,
            $values['payable'], $values['auction']);
        $this->modal()->hide();

        $this->cl(Total::class)->render();
        $this->cl(PayablePage::class)->render();
    }

    /**
     * @param int $payableId
     */
    public function deleteRemitment(int $payableId): void
    {
        $pool = $this->stash()->get('meeting.pool');
        $session = $this->stash()->get('meeting.session');
        $this->remitmentService->deleteRemitment($pool, $session, $payableId);

        $this->cl(Total::class)->render();
        $this->cl(PayablePage::class)->render();
    }
}
