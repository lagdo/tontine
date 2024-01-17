<?php

namespace App\Exceptions;

use App\Ajax\Web\Meeting\Session;
use App\Ajax\Web\Planning\Pool;
use App\Ajax\Web\Planning\Round;
use App\Ajax\Web\Tontine\Member;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jaxon\Laravel\Jaxon;
use Siak\Tontine\Exception\AuthenticationException;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Exception\MeetingRoundException;
use Siak\Tontine\Exception\PlanningPoolException;
use Siak\Tontine\Exception\PlanningRoundException;
use Siak\Tontine\Exception\TontineMemberException;
use Throwable;

use function app;
use function route;
use function trans;

class Handler extends ExceptionHandler
{
    /**
     * @var Jaxon
     */
    private Jaxon $jaxon;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthenticationException::class,
        MessageException::class,
        PlanningPoolException::class,
        PlanningRoundException::class,
        TontineMemberException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->jaxon = app()->make(Jaxon::class);

        $this->reportable(function (Throwable $e) {
            //
        });

        // Redirect to the login page
        $this->renderable(function (AuthenticationException $e) {
            $ajaxResponse = $this->jaxon->ajaxResponse();
            $ajaxResponse->redirect(route('login'));

            return $this->jaxon->httpResponse();
        });

        // Show the error message in a dialog
        $this->renderable(function (MessageException $e) {
            $ajaxResponse = $this->jaxon->ajaxResponse();
            $ajaxResponse->dialog->error($e->getMessage(), trans('common.titles.error'));

            return $this->jaxon->httpResponse();
        });

        // Show the warning message in a dialog, and show the sessions page.
        $this->renderable(function (PlanningRoundException $e) {
            $this->jaxon->cl(Round::class)->home();

            $ajaxResponse = $this->jaxon->ajaxResponse();
            $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));

            return $this->jaxon->httpResponse();
        });

        // Show the warning message in a dialog, and show the pools page.
        $this->renderable(function (PlanningPoolException $e) {
            $this->jaxon->cl(Pool::class)->home();

            $ajaxResponse = $this->jaxon->ajaxResponse();
            $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));

            return $this->jaxon->httpResponse();
        });

        // Show the warning message in a dialog, and show the members page.
        $this->renderable(function (TontineMemberException $e) {
            $this->jaxon->cl(Member::class)->home();

            $ajaxResponse = $this->jaxon->ajaxResponse();
            $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));

            return $this->jaxon->httpResponse();
        });

        // Show the warning message in a dialog, and show the sessions page.
        $this->renderable(function (MeetingRoundException $e) {
            $this->jaxon->cl(Session::class)->home();

            $ajaxResponse = $this->jaxon->ajaxResponse();
            $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));

            return $this->jaxon->httpResponse();
        });
    }
}
