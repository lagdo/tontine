<?php

namespace App\Ajax\App\Planning;

use App\Ajax\CallableClass;
use Carbon\Carbon;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Planning\SessionValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag planning
 */
class Session extends CallableClass
{
    /**
     * @di
     * @var TontineService
     */
    protected TontineService $tontineService;

    /**
     * @di
     * @var SessionService
     */
    public SessionService $sessionService;

    /**
     * @var SessionValidator
     */
    protected SessionValidator $validator;

    /**
     * @after hideMenuOnMobile
     */
    public function home()
    {
        $html = $this->view()->render('tontine.pages.planning.session.home');
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());
        $this->jq('#btn-create')->click($this->rq()->number());

        return $this->page($this->bag('planning')->get('session.page', 1));
    }

    public function page(int $pageNumber = 0)
    {
        $sessionCount = $this->sessionService->getSessionCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $sessionCount, 'planning', 'session.page');
        $sessions = $this->sessionService->getSessions($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $sessionCount);

        $statuses = [
            SessionModel::STATUS_PENDING => trans('tontine.session.status.pending'),
            SessionModel::STATUS_OPENED => trans('tontine.session.status.opened'),
            SessionModel::STATUS_CLOSED => trans('tontine.session.status.closed'),
        ];

        $html = $this->view()->render('tontine.pages.planning.session.page')
            ->with('sessions', $sessions)
            ->with('statuses', $statuses)
            ->with('members', $this->tontineService->getMembers())
            ->with('pagination', $pagination);
        $this->response->html('content-page', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-session-edit')->click($this->rq()->edit($sessionId));
        $this->jq('.btn-session-venue')->click($this->rq()->editVenue($sessionId));
        $this->jq('.btn-session-delete')->click($this->rq()->del($sessionId)
            ->confirm(trans('tontine.session.questions.delete')));

        return $this->response;
    }

    public function number()
    {
        $title = trans('number.labels.title');
        $content = $this->view()->render('tontine.pages.planning.session.number');
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.year'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->years(),
        ],[
            'title' => trans('common.actions.add'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->add(pm()->input('text-number')->toInt()),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function add(int $count)
    {
        if($count <= 0)
        {
            $this->notify->warning(trans('number.errors.invalid'));
            return $this->response;
        }
        if($count > 12)
        {
            $this->notify->warning(trans('number.errors.max', ['max' => 12]));
            return $this->response;
        }

        $this->dialog->hide();

        $html = $this->view()->render('tontine.pages.planning.session.add')->with('count', $count);
        $this->response->html('content-home', $html);
        $this->jq('#btn-cancel')->click($this->rq()->home());
        $this->jq('#btn-copy')->click($this->rq()->copy(jq('#session_date_0')->val(), $count));
        $this->jq('#btn-save')->click($this->rq()->create(pm()->form('session-form')));

        return $this->response;
    }

    public function years()
    {
        $count = 12;
        $this->add($count);
        $sessions = $this->sessionService->getYearSessions();
        for($i = 0; $i < $count; $i++)
        {
            $this->jq("#session_title_$i")->val($sessions[$i]->title);
            $this->jq("#session_date_$i")->val($sessions[$i]->date);
        }

        return $this->response;
    }

    public function copy(string $date, int $count)
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $date = Carbon::createFromFormat('Y-m-d', $date);
        $this->jq("#session_title_0")->val(trans('tontine.session.titles.title',
            ['month' => $date->locale($locale)->monthName, 'year' => $date->year]));
        for($i = 1; $i < $count; $i++)
        {
            $date->addMonth();
            $this->jq("#session_title_$i")->val(trans('tontine.session.titles.title',
                ['month' => $date->locale($locale)->monthName, 'year' => $date->year]));
            $this->jq("#session_date_$i")->val($date->toDateString());
        }

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function create(array $formValues)
    {
        $values = $this->validator->validateList($formValues['sessions'] ?? []);

        $this->sessionService->createSessions($values);
        $this->notify->success(trans('tontine.session.messages.created'), trans('common.titles.success'));

        return $this->home();
    }

    public function edit(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);

        $title = trans('tontine.session.titles.edit');
        $content = $this->view()->render('tontine.pages.planning.session.edit')
            ->with('session', $session)
            ->with('members', $this->tontineService->getMembers());
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
        $content = $this->view()->render('tontine.pages.planning.session.venue')
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

    public function del(int $sessionId)
    {
        $session = $this->sessionService->getSession($sessionId);
        $this->sessionService->deleteSession($session);

        return $this->page();
    }
}
