<?php

namespace App\Exceptions;

use App\Ajax\Web\Meeting\Session;
use App\Ajax\Web\Planning\Pool;
use App\Ajax\Web\Planning\Round;
use App\Ajax\Web\Tontine\Member;
use App\Ajax\Web\Tontine\Tontine;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jaxon\Laravel\Jaxon;
use Siak\Tontine\Exception\AuthenticationException;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Exception\MeetingRoundException;
use Siak\Tontine\Exception\PlanningPoolException;
use Siak\Tontine\Exception\PlanningRoundException;
use Siak\Tontine\Exception\TontineMemberException;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\ValidationException;
use Throwable;

use function app;
use function route;
use function trans;

class Handler extends ExceptionHandler
{
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
        ValidationException::class,
        PlanningPoolException::class,
        PlanningRoundException::class,
        TontineMemberException::class,
        MeetingRoundException::class,
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
     * Check guest user access before redirection
     *
     * @param Jaxon $jaxon
     * @param string $section
     * @param string $entry
     *
     * @return bool
     */
    private function checkGuestAccess(Jaxon $jaxon, string $section, string $entry): bool
    {
        /** @var TenantService */
        $tenantService = app()->make(TenantService::class);
        if(!($access = $tenantService->checkGuestAccess($section, $entry, true)))
        {
            $jaxon->cl(Tontine::class)->home();
        }
        return $access;
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Redirect to the login page
        $this->renderable(function (AuthenticationException $e) {
            /** @var Jaxon */
            $jaxon = app()->make(Jaxon::class);
            $ajaxResponse = $jaxon->ajaxResponse();
            $ajaxResponse->redirect(route('login'));

            return $jaxon->httpResponse();
        });

        // Show the error message in a dialog
        $this->renderable(function (MessageException $e) {
            /** @var Jaxon */
            $jaxon = app()->make(Jaxon::class);
            $ajaxResponse = $jaxon->ajaxResponse();
            $e->isError ?
                $ajaxResponse->dialog->error($e->getMessage(), trans('common.titles.error')) :
                $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));

            return $jaxon->httpResponse();
        });

        // Show the error message in a dialog
        $this->renderable(function (ValidationException $e) {
            /** @var Jaxon */
            $jaxon = app()->make(Jaxon::class);
            $ajaxResponse = $jaxon->ajaxResponse();
            $ajaxResponse->dialog->error($e->getMessage(), trans('common.titles.error'));

            return $jaxon->httpResponse();
        });

        // Show the warning message in a dialog, and show the sessions page.
        $this->renderable(function (PlanningRoundException $e) {
            /** @var Jaxon */
            $jaxon = app()->make(Jaxon::class);
            if($this->checkGuestAccess($jaxon, 'planning', 'sessions'))
            {
                $jaxon->cl(Round::class)->home();
            }

            $ajaxResponse = $jaxon->ajaxResponse();
            $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));
            return $jaxon->httpResponse();
        });

        // Show the warning message in a dialog, and show the pools page.
        $this->renderable(function (PlanningPoolException $e) {
            /** @var Jaxon */
            $jaxon = app()->make(Jaxon::class);
            if($this->checkGuestAccess($jaxon, 'planning', 'pools'))
            {
                $jaxon->cl(Pool::class)->home();
            }

            $ajaxResponse = $jaxon->ajaxResponse();
            $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));
            return $jaxon->httpResponse();
        });

        // Show the warning message in a dialog, and show the members page.
        $this->renderable(function (TontineMemberException $e) {
            /** @var Jaxon */
            $jaxon = app()->make(Jaxon::class);
            if($this->checkGuestAccess($jaxon, 'tontine', 'members'))
            {
                $jaxon->cl(Member::class)->home();
            }

            $ajaxResponse = $jaxon->ajaxResponse();
            $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));
            return $jaxon->httpResponse();
        });

        // Show the warning message in a dialog, and show the sessions page.
        $this->renderable(function (MeetingRoundException $e) {
            /** @var Jaxon */
            $jaxon = app()->make(Jaxon::class);
            if($this->checkGuestAccess($jaxon, 'meeting', 'sessions'))
            {
                $jaxon->cl(Session::class)->home();
            }

            $ajaxResponse = $jaxon->ajaxResponse();
            $ajaxResponse->dialog->warning($e->getMessage(), trans('common.titles.warning'));
            return $jaxon->httpResponse();
        });
    }
}
