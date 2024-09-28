<?php

namespace App\Ajax\Web\Report\Session;

use App\Ajax\Component as BaseComponent;
use Siak\Tontine\Model\Member as MemberModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Report\MemberService;
use Siak\Tontine\Service\Report\SessionService;

abstract class Component extends BaseComponent
{
    /**
     * @var SessionModel
     */
    protected SessionModel $session;

    /**
     * @var MemberModel
     */
    protected ?MemberModel $member;

    /**
     * @param MemberService $memberService
     * @param SessionService $sessionService
     */
    public function __construct(protected MemberService $memberService,
        protected SessionService $sessionService)
    {}

    /**
     * @exclude
     */
    public function init(SessionModel $session, MemberModel $member = null)
    {
        $this->session = $session;
        $this->member = $member;
    }
}
