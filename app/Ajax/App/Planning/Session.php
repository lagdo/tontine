<?php

namespace App\Ajax\App\Planning;

use App\Ajax\CallableClass;
use Carbon\Carbon;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Validation\Planning\SessionValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag session
 */
class Session extends CallableClass
{
    /**
     * @di
     * @var SessionService
     */
    public SessionService $sessionService;

    /**
     * @var SessionValidator
     */
    protected SessionValidator $validator;

    public function home()
    {
        $html = $this->view()->render('tontine.pages.planning.session.home');
        $this->response->html('section-title', trans('tontine.menus.planning'));
        $this->response->html('content-home', $html);
        $this->jq('#btn-refresh')->click($this->rq()->home());
        $this->jq('#btn-create')->click($this->rq()->number());

        return $this->page($this->bag('session')->get('page', 1));
    }

    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('session')->get('page', 1);
        }
        $this->bag('session')->set('page', $pageNumber);

        $sessions = $this->sessionService->getSessions($pageNumber);
        $sessionCount = $this->sessionService->getSessionCount();

        $html = $this->view()->render('tontine.pages.planning.session.page')
            ->with('sessions', $sessions)
            ->with('members', $this->sessionService->getMembers())
            ->with('pagination', $this->rq()->page()->paginate($pageNumber, 10, $sessionCount));
        $this->response->html('content-page', $html);

        $sessionId = jq()->parent()->attr('data-session-id')->toInt();
        $this->jq('.btn-session-edit')->click($this->rq()->edit($sessionId));
        $this->jq('.btn-session-venue')->click($this->rq()->editVenue($sessionId));

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
        $this->jq('#btn-copy')->click($this->rq()->copy(jq('#session_date_0')->val(),
            jq('#session_start_0')->val(), jq('#session_end_0')->val(), $count));
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
            $this->jq("#session_start_$i")->val($sessions[$i]->start);
            $this->jq("#session_end_$i")->val($sessions[$i]->end);
        }

        return $this->response;
    }

    public function copy(string $date, string $start, string $end, int $count)
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
            $this->jq("#session_start_$i")->val($start);
            $this->jq("#session_end_$i")->val($end);
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
        $content = $this->view()->render('tontine.pages.planning.session.edit')->with('session', $session)
            ->with('members', $this->sessionService->getMembers());
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($session->id, pm()->form('session-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

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
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

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

        return  $this->page();
    }

    /*public function delete(int $sessionId)
    {
        $this->notify->error("Cette fonction n'est pas encore disponible", trans('common.titles.error'));

        return $this->response;
    }*/
}
