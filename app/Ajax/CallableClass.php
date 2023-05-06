<?php

namespace App\Ajax;

use App\Ajax\App\Balance\Meeting\Round as RoundBalance;
use App\Ajax\App\Balance\Meeting\Session as SessionBalance;
use App\Ajax\App\Balance\Planning as PlanningBalance;
use App\Ajax\App\Meeting\Session as MeetingSession;
use App\Ajax\App\Planning\Planning;
use App\Ajax\App\Planning\Pool;
use App\Ajax\App\Planning\Session as PlanningSession;
use App\Ajax\App\Tontine\Charge;
use App\Ajax\App\Tontine\Member;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Model\User;
use Jaxon\App\CallableClass as JaxonCallableClass;
use Jaxon\App\Dialog\MessageInterface;
use Jaxon\App\Dialog\ModalInterface;

use function floor;

class CallableClass extends JaxonCallableClass
{
    /**
     * @var User|null
     */
    public ?User $user;

    /**
     * @var Tontine|null
     */
    public ?Tontine $tontine;

    /**
     * @var ModalInterface
     */
    public $dialog;

    /**
     * @var MessageInterface
     */
    public $notify;

    /**
     * Get the page number to show
     *
     * @param int $pageNumber
     * @param int $itemCount
     * @param string $bagName
     * @param string $attrName
     *
     * @return array
     */
    protected function pageNumber(int $pageNumber, int $itemCount, string $bagName, string $attrName = 'page'): array
    {
        $perPage = 10;
        $pageCount = (int)floor($itemCount / $perPage) + ($itemCount % $perPage > 0 ? 1 : 0);
        if($pageNumber > $pageCount)
        {
            $pageNumber = $pageCount;
        }
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag($bagName)->get($attrName, 1);
        }
        $this->bag($bagName)->set($attrName, $pageNumber);

        return [$pageNumber, 10];
    }

    /**
     * @param Tontine $tontine
     *
     * @return void
     */
    protected function selectTontine(Tontine $tontine)
    {
        $this->response->html('section-tontine-name', $tontine->name);

        // Set the tontine sidebar menu
        $this->response->html('sidebar-menu-tontine', $this->view()->render('tontine.parts.sidebar.tontine'));
        $this->jq('a', '#sidebar-menu-tontine')->css('color', '#6777ef');

        $this->jq('#tontine-menu-members')->click($this->cl(Member::class)->rq()->home());
        $this->jq('#tontine-menu-charges')->click($this->cl(Charge::class)->rq()->home());

        // Reset the round sidebar menu
        $this->response->html('sidebar-menu-round', $this->view()->render('tontine.parts.sidebar.round'));
    }

    /**
     * @param Round $round
     *
     * @return void
     */
    protected function selectRound(Round $round)
    {
        $this->response->html('section-tontine-name', $round->tontine->name . ' - ' . $round->title);

        // Set the round sidebar menu
        $this->response->html('sidebar-menu-round', $this->view()->render('tontine.parts.sidebar.round'));
        $this->jq('a', '#sidebar-menu-round')->css('color', '#6777ef');

        $this->jq('#planning-menu-subscriptions')->click($this->cl(Pool::class)->rq()->home());
        $this->jq('#planning-menu-sessions')->click($this->cl(PlanningSession::class)->rq()->home());
        $this->jq('#planning-menu-beneficiaries')->click($this->cl(Planning::class)->rq()->beneficiaries());
        $this->jq('#planning-menu-balance')->click($this->cl(PlanningBalance::class)->rq()->home());
        $this->jq('#meeting-menu-sessions')->click($this->cl(MeetingSession::class)->rq()->home());
        $this->jq('#balance-menu-session')->click($this->cl(SessionBalance::class)->rq()->home());
        $this->jq('#balance-menu-round')->click($this->cl(RoundBalance::class)->rq()->home());
    }
}
