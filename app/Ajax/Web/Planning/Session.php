<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\CallableClass;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Planning\SessionValidator;

use function Jaxon\jq;
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
 */
class Session extends CallableClass
{
    /**
     * @var SessionValidator
     */
    protected SessionValidator $validator;

    public function __construct(private TontineService $tontineService,
        private MemberService $memberService, private SessionService $sessionService)
    {}

    /**
     * @exclude
     */
    public function show()
    {
        return $this->home();
    }

    public function home()
    {
        $round = $this->tenantService->round();
        $html = $this->render('pages.planning.session.home', ['round' => $round]);
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home-sessions', $html);

        if(!$round)
        {
            return $this->response;
        }

        $this->jq('#btn-sessions-refresh')->click($this->rq()->home());
        $this->jq('#btn-sessions-add')->click($this->rq()->add());
        $this->jq('#btn-sessions-add-list')->click($this->rq()->addList());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->sessionService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount,
            'planning', 'session.page');
        $sessions = $this->sessionService->getSessions($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);

        $statuses = [
            SessionModel::STATUS_PENDING => trans('tontine.session.status.pending'),
            SessionModel::STATUS_OPENED => trans('tontine.session.status.opened'),
            SessionModel::STATUS_CLOSED => trans('tontine.session.status.closed'),
        ];

        $html = $this->render('pages.planning.session.page')
            ->with('sessions', $sessions)
            ->with('statuses', $statuses)
            ->with('pagination', $pagination);
        $this->response->html('content-page-sessions', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-session-edit')->click($this->rq()->edit($sessionId));
        $this->jq('.btn-session-venue')->click($this->rq()->editVenue($sessionId));
        $this->jq('.btn-session-delete')->click($this->rq()->delete($sessionId)
            ->confirm(trans('tontine.session.questions.delete')));

        return $this->response;
    }

    public function add()
    {
        $title = trans('tontine.session.titles.add');
        $content = $this->render('pages.planning.session.add')
            ->with('members', $this->memberService->getMemberList()->prepend('', 0));
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
        $this->sessionService->createSession($values);
        $this->dialog->hide();
        $this->notify->success(trans('tontine.session.messages.created'), trans('common.titles.success'));

        return $this->page();
    }

    public function addList()
    {
        $title = trans('tontine.session.titles.add-list');
        $content = $this->render('pages.planning.session.list');
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

        $this->sessionService->createSessions($values);
        $this->dialog->hide();
        $this->notify->success(trans('tontine.session.messages.created'), trans('common.titles.success'));

        return $this->home();
    }

    public function edit(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);
        $title = trans('tontine.session.titles.edit');
        $content = $this->render('pages.planning.session.edit')
            ->with('session', $session)
            ->with('members', $this->memberService->getMemberList()->prepend('', 0));
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
        $session = $this->sessionService->getSession($sessionId);

        $this->sessionService->updateSession($session, $values);
        $this->dialog->hide();
        $this->notify->success(trans('tontine.session.messages.updated'), trans('common.titles.success'));

        return  $this->page();
    }

    public function editVenue(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);

        $venue = $session->venue ?? ($session->host ? $session->host->address : '');
        $title = trans('tontine.session.titles.venue');
        $content = $this->render('pages.planning.session.venue')
            ->with('session', $session)->with('venue', $venue);
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
        $session = $this->sessionService->getSession($sessionId);

        $this->sessionService->saveSessionVenue($session, $values);
        $this->dialog->hide();
        $this->notify->success(trans('tontine.session.messages.updated'), trans('common.titles.success'));

        return $this->page();
    }

    public function delete(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);
        $this->sessionService->deleteSession($session);
        $this->notify->success(trans('tontine.session.messages.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
