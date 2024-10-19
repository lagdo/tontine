<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\CallableClass;
use App\Ajax\Web\SectionTitle;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Round as RoundModel;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Planning\SessionValidator;

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
 * @databag planning
 * @before getRound
 */
class Session extends CallableClass
{
    /**
     * @var MemberService
     */
    protected MemberService $memberService;

    /**
     * @var SessionValidator
     */
    protected SessionValidator $validator;

    /**
     * @var RoundModel|null
     */
    private ?RoundModel $round = null;

    public function __construct(private RoundService $roundService,
        private SessionService $sessionService)
    {}

    /**
     * @exclude
     */
    public function show(?RoundModel $round)
    {
        $this->round = $round;
        return $this->home(!$round ? 0 : $round->id, false);
    }

    /**
     * @return void
     */
    protected function getRound(): void
    {
        $roundId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('planning')->get('round.id');
        $this->round = $this->roundService->getRound($roundId);
    }

    public function home(int $roundId, bool $showScreen = true)
    {
        $this->bag('planning')->set('round.id', $roundId);

        $this->cl(SectionTitle::class)->show(trans('tontine.menus.planning'));
        $html = $this->renderView('pages.planning.session.home', [
            'round' => $this->round,
        ]);
        $this->response->html('content-home-sessions', $html);

        if($showScreen)
        {
            $this->response->js()->showSmScreen('content-home-sessions', 'round-sm-screens');
        }

        return $this->cl(SessionPage::class)->page();
    }

    /**
     * @di $memberService
     */
    public function add()
    {
        $title = trans('tontine.session.titles.add');
        $content = $this->renderView('pages.planning.session.add', [
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);
        $this->sessionService->createSession($this->round, $values);
        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.created'));

        return $this->cl(SessionPage::class)->page();
    }

    public function addList()
    {
        $title = trans('tontine.session.titles.add-list');
        $content = $this->renderView('pages.planning.session.list');
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function years()
    {
        $sessions = $this->sessionService->getYearSessions();
        $html = collect($sessions)->map(function($session) {
            return $session->title . ';' . $session->date;
        })->join("\n");
        $this->response->html('new-sessions-list', $html);

        return $this->response;
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
                'date' => trim($values[1]),
                'start' => '00:00',
                'end' => '00:00',
                'host_id' => 0,
            ];
        }, explode("\n", trim($sessions, " \t\n\r;")));
        // Filter empty lines.
        $sessions = array_filter($sessions, fn($session) => count($session) > 0);

        // Check uniqueness of session dates
        $sessionDates = array_unique(array_map(fn($session) => $session['date'], $sessions));
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
        $values = $this->parseSessionList($formValues['sessions'] ?? '');

        $this->sessionService->createSessions($this->round, $values);
        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.created'));

        return $this->cl(SessionPage::class)->page();
    }

    /**
     * @di $memberService
     */
    public function edit(int $sessionId)
    {
        $session = $this->roundService->getSession($this->round, $sessionId);
        $title = trans('tontine.session.titles.edit');
        $content = $this->renderView('pages.planning.session.edit', [
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function update(int $sessionId, array $formValues)
    {
        $formValues['id'] = $sessionId;
        $values = $this->validator->validateItem($formValues);
        $session = $this->roundService->getSession($this->round, $sessionId);

        $this->sessionService->updateSession($session, $values);
        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.updated'));

        return  $this->cl(SessionPage::class)->page();
    }

    public function editVenue(int $sessionId)
    {
        $session = $this->roundService->getSession($this->round, $sessionId);

        $title = trans('tontine.session.titles.venue');
        $content = $this->renderView('pages.planning.session.venue', [
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveVenue(int $sessionId, array $formValues)
    {
        $values = $this->validator->validateVenue($formValues);
        $session = $this->roundService->getSession($this->round, $sessionId);

        $this->sessionService->saveSessionVenue($session, $values);
        $this->dialog->hide();
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.updated'));

        return $this->cl(SessionPage::class)->page();
    }

    public function delete(int $sessionId)
    {
        $session = $this->roundService->getSession($this->round, $sessionId);
        $this->sessionService->deleteSession($session);
        $this->notify->title(trans('common.titles.success'))
            ->success(trans('tontine.session.messages.deleted'));

        return $this->cl(SessionPage::class)->page();
    }
}
