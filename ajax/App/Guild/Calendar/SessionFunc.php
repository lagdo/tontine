<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\Base\Guild\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Jaxon\Attributes\Attribute\Databag;
use Jaxon\Attributes\Attribute\Inject;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Guild\RoundService;
use Siak\Tontine\Service\Guild\SessionService;
use Siak\Tontine\Service\Meeting\Member\MemberService;
use Siak\Tontine\Validation\Guild\SessionValidator;

use function je;
use function array_filter;
use function array_map;
use function array_unique;
use function collect;
use function count;
use function explode;
use function trans;
use function trim;

#[Before('checkHostAccess', ["guild", "calendar"])]
#[Before('getRound')]
#[Databag('guild.calendar')]
class SessionFunc extends FuncComponent
{
    /**
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @var SessionValidator
     */
    protected SessionValidator $validator;

    public function __construct(private RoundService $roundService,
        private SessionService $sessionService)
    {}

    /**
     * @return void
     */
    protected function getRound(): void
    {
        $roundId = $this->bag('guild.calendar')->get('round.id');
        $round = $this->roundService->getRound($this->guild(), $roundId);
        $this->stash()->set('guild.calendar.round', $round);
    }

    #[Inject(attr: 'memberService')]
    public function add(): void
    {
        $round = $this->stash()->get('guild.calendar.round');
        $title = trans('tontine.session.titles.add');
        $content = $this->renderView('pages.guild.calendar.session.add', [
            'members' => $this->memberService->getMemberList($round)->prepend('', 0),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(je('session-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function create(array $formValues): void
    {
        $round = $this->stash()->get('guild.calendar.round');
        $values = $this->validator->validateItem($formValues);
        $this->sessionService->createSession($round, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.created'));

        $this->cl(SessionPage::class)->page();
    }

    public function addList(): void
    {
        $title = trans('tontine.session.titles.add-list');
        $content = $this->renderView('pages.guild.calendar.session.list');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.year'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->years(),
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createList(je('session-list')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function years(): void
    {
        $sessions = $this->sessionService->getYearSessions();
        $html = collect($sessions)
            ->map(fn($session) => $session->title . ';' . $session->date)
            ->join("\n");
        $this->response->html('new-sessions-list', $html);
    }

    /**
     * @param string $sessions
     *
     * @return array
     */
    private function parseSessionList(string $sessions): array
    {
        $sessions = array_map(function($value) {
            if(!($value = trim($value, " \t\n\r;")))
            {
                return [];
            }
            $values = explode(";", $value);
            if(count($values) !== 2)
            {
                return []; // Todo: throw an exception
            }
            return [
                'title' => trim($values[0]),
                'day_date' => trim($values[1]),
                'start_time' => '00:00',
                'end_time' => '00:00',
                'host_id' => 0,
            ];
        }, explode("\n", trim($sessions, " \t\n\r;")));
        // Filter empty lines.
        $sessions = array_filter($sessions, fn($session) => count($session) > 0);

        // Check uniqueness of session dates
        $sessionDates = array_unique(
            array_map(fn($session) => $session['day_date'], $sessions)
        );
        if(count($sessions) !== count($sessionDates))
        {
            throw new MessageException(trans('tontine.session.errors.date_dup'));
        }

        return $this->validator->validateList($sessions);
    }

    #[Inject(attr: 'validator')]
    public function createList(array $formValues): void
    {
        $round = $this->stash()->get('guild.calendar.round');
        $values = $this->parseSessionList($formValues['sessions'] ?? '');

        $this->sessionService->createSessions($round, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.created'));

        $this->cl(SessionPage::class)->page();
    }

    #[Inject(attr: 'memberService')]
    public function edit(int $sessionId): void
    {
        $round = $this->stash()->get('guild.calendar.round');
        $session = $this->roundService->getSession($round, $sessionId);
        $title = trans('tontine.session.titles.edit');
        $content = $this->renderView('pages.guild.calendar.session.edit', [
            'session' => $session,
            'members' => $this->memberService->getMemberList($round)->prepend('', 0),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($session->id, je('session-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function update(int $sessionId, array $formValues): void
    {
        $round = $this->stash()->get('guild.calendar.round');
        $formValues['id'] = $sessionId;
        $values = $this->validator->validateItem($formValues);
        $session = $this->roundService->getSession($round, $sessionId);

        $this->sessionService->updateSession($this->guild(), $session, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.updated'));

        $this->cl(SessionPage::class)->page();
    }

    public function editVenue(int $sessionId): void
    {
        $round = $this->stash()->get('guild.calendar.round');
        $session = $this->roundService->getSession($round, $sessionId);

        $title = trans('tontine.session.titles.venue');
        $content = $this->renderView('pages.guild.calendar.session.venue', [
            'session' => $session,
            'venue' => $session->venue ?? ($session->host ? $session->host->address : ''),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveVenue($session->id, je('session-form')->rd()->form()),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    #[Inject(attr: 'validator')]
    public function saveVenue(int $sessionId, array $formValues): void
    {
        $round = $this->stash()->get('guild.calendar.round');
        $values = $this->validator->validateVenue($formValues);
        $session = $this->roundService->getSession($round, $sessionId);

        $this->sessionService->saveSessionVenue($session, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.updated'));

        $this->cl(SessionPage::class)->page();
    }

    public function delete(int $sessionId): void
    {
        $round = $this->stash()->get('guild.calendar.round');
        $session = $this->roundService->getSession($round, $sessionId);
        $this->sessionService->deleteSession($session);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.deleted'));

        $this->cl(SessionPage::class)->page();
    }
}
