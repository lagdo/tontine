<?php

namespace App\Ajax\Web\Meeting\Presence;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Meeting\PresenceService;

use function trim;

/**
 * @databag presence
 */
class MemberPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['presence', 'member.page'];

    /**
     * @param PresenceService $presenceService
     */
    public function __construct(private PresenceService $presenceService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        $search = trim($this->bag('presence')->get('member.search', ''));
        return $this->presenceService->getMemberCount($search);
    }

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        $session = $this->cl(Home::class)->getSession(); // Is null when showing presences by members.
        $search = trim($this->bag('presence')->get('member.search', ''));

        return (string)$this->renderView('pages.meeting.presence.member.page', [
            'session' => $session,
            'search' => $search,
            'members' => $this->presenceService->getMembers($search, $this->page),
            'absences' => !$session ? null :
                $this->presenceService->getSessionAbsences($session),
            'sessionCount' => $this->presenceService->getSessionCount(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page-members');
    }
}
