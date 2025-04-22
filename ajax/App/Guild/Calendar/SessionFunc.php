<?php

namespace Ajax\App\Guild\Calendar;

use Ajax\FuncComponent;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Service\Guild\RoundService;
use Siak\Tontine\Service\Guild\SessionService;
use Siak\Tontine\Validation\Guild\SessionValidator;

use function Jaxon\pm;
use function array_filter;
use function array_map;
use function array_unique;
use function collect;
use function count;
use function explode;
use function trans;
use function trim;

/**
 * @databag planning.calendar
 * @before getRound
 */
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
        $roundId = $this->bag('planning.calendar')->get('round.id');
        $this->stash()->set('planning.calendar.round', $this->roundService->getRound($roundId));
    }

    /**
     * @di $memberService
     */
    public function add()
    {
        $title = trans('tontine.session.titles.add');
        $content = $this->renderView('pages.guild.calendar.session.add', [
            'members' => $this->memberService->getMemberList()->prepend('', 0),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('session-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $round = $this->stash()->get('planning.calendar.round');
        $values = $this->validator->validateItem($formValues);
        $this->sessionService->createSession($round, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.created'));

        $this->cl(SessionPage::class)->page();
    }

    public function addList()
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
            'click' => $this->rq()->createList(pm()->form('session-list')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function years()
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

    /**
     * @di $validator
     */
    public function createList(array $formValues)
    {
        $round = $this->stash()->get('planning.calendar.round');
        $values = $this->parseSessionList($formValues['sessions'] ?? '');

        $this->sessionService->createSessions($round, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.created'));

        $this->cl(SessionPage::class)->page();
    }

    /**
     * @di $memberService
     */
    public function edit(int $sessionId)
    {
        $round = $this->stash()->get('planning.calendar.round');
        $session = $this->roundService->getSession($round, $sessionId);
        $title = trans('tontine.session.titles.edit');
        $content = $this->renderView('pages.guild.calendar.session.edit', [
            'session' => $session,
            'members' => $this->memberService->getMemberList()->prepend('', 0),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($session->id, pm()->form('session-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function update(int $sessionId, array $formValues)
    {
        $round = $this->stash()->get('planning.calendar.round');
        $formValues['id'] = $sessionId;
        $values = $this->validator->validateItem($formValues);
        $session = $this->roundService->getSession($round, $sessionId);

        $this->sessionService->updateSession($session, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.updated'));

        $this->cl(SessionPage::class)->page();
    }

    public function editVenue(int $sessionId)
    {
        $round = $this->stash()->get('planning.calendar.round');
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
            'click' => $this->rq()->saveVenue($session->id, pm()->form('session-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    /**
     * @di $validator
     */
    public function saveVenue(int $sessionId, array $formValues)
    {
        $round = $this->stash()->get('planning.calendar.round');
        $values = $this->validator->validateVenue($formValues);
        $session = $this->roundService->getSession($round, $sessionId);

        $this->sessionService->saveSessionVenue($session, $values);
        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.updated'));

        $this->cl(SessionPage::class)->page();
    }

    public function delete(int $sessionId)
    {
        $round = $this->stash()->get('planning.calendar.round');
        $session = $this->roundService->getSession($round, $sessionId);
        $this->sessionService->deleteSession($session);
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.deleted'));

        $this->cl(SessionPage::class)->page();
    }
}
