<?php

namespace App\Ajax;

use App\Ajax\Web\Meeting\Session as MeetingSession;
use App\Ajax\Web\Planning\Pool;
use App\Ajax\Web\Planning\Session as PlanningSession;
use App\Ajax\Web\Planning\Subscription;
use App\Ajax\Web\Report\Round as RoundReport;
use App\Ajax\Web\Report\Session as SessionReport;
use App\Ajax\Web\Tontine\Charge;
use App\Ajax\Web\Tontine\Member;
use App\Ajax\Web\Tontine\Round as TontineRound;
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
        $this->jq('#planning-menu-rounds')->click($this->cl(TontineRound::class)->rq()->home());

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

        $this->jq('#planning-menu-sessions')->click($this->cl(PlanningSession::class)->rq()->home());
        $this->jq('#planning-menu-pools')->click($this->cl(Pool::class)->rq()->home());
        $this->jq('#planning-menu-subscriptions')->click($this->cl(Subscription::class)->rq()->home());
        $this->jq('#meeting-menu-sessions')->click($this->cl(MeetingSession::class)->rq()->home());
        $this->jq('#report-menu-session')->click($this->cl(SessionReport::class)->rq()->home());
        $this->jq('#report-menu-round')->click($this->cl(RoundReport::class)->rq()->home());
    }

    /**
     * @return void
     */
    protected function hideMenuOnMobile()
    {
        // The current template main menu doesn't hide automatically
        // after a click on mobile devices. We need to do that manually.
        $this->jq('body')->trigger('touchend');
    }
}
