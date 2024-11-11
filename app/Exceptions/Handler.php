<?php

namespace App\Exceptions;

use App\Ajax\Web\Meeting\Session\Session;
use App\Ajax\Web\Planning\Pool\Pool;
use App\Ajax\Web\Planning\Session\Round;
use App\Ajax\Web\Tontine\Member\Member;
use App\Ajax\Web\Tontine\Organisation;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Jaxon\Laravel\App\Jaxon;
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
use function Jaxon\cl;
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
     * @param string $section
     * @param string $entry
     *
     * @return bool
     */
    private function checkGuestAccess(string $section, string $entry): bool
    {
        /** @var TenantService */
        $tenantService = app()->make(TenantService::class);
        if(!($access = $tenantService->checkGuestAccess($section, $entry, true)))
        {
            cl(Organisation::class)->home();
        }
        return $access;
    }

    /**
     * @param string $message
     * @param bool $isError
     *
     * @return Response
     */
    private function showMessage(string $message, bool $isError): Response
    {
        /** @var Jaxon */
        $jaxon = app()->make(Jaxon::class);
        $ajaxResponse = $jaxon->ajaxResponse();
        $messageType = $isError ? 'error' : 'warning';
        $ajaxResponse->dialog
            ->title(trans("common.titles.$messageType"))
            ->$messageType($message);

        return $jaxon->httpResponse();
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
            return $this->showMessage($e->getMessage(), $e->isError);
        });

        // Show the error message in a dialog
        $this->renderable(function (ValidationException $e) {
            return $this->showMessage($e->getMessage(), true);
        });

        // Show the warning message in a dialog, and show the sessions page.
        $this->renderable(function (PlanningRoundException $e) {
            if($this->checkGuestAccess('planning', 'sessions'))
            {
                cl(Round::class)->home();
            }

            return $this->showMessage($e->getMessage(), false);
        });

        // Show the warning message in a dialog, and show the pools page.
        $this->renderable(function (PlanningPoolException $e) {
            if($this->checkGuestAccess('planning', 'pools'))
            {
                cl(Pool::class)->home();
            }

            return $this->showMessage($e->getMessage(), false);
        });

        // Show the warning message in a dialog, and show the members page.
        $this->renderable(function (TontineMemberException $e) {
            if($this->checkGuestAccess('tontine', 'members'))
            {
                cl(Member::class)->home();
            }

            return $this->showMessage($e->getMessage(), false);
        });

        // Show the warning message in a dialog, and show the sessions page.
        $this->renderable(function (MeetingRoundException $e) {
            if($this->checkGuestAccess('meeting', 'sessions'))
            {
                cl(Session::class)->home();
            }

            return $this->showMessage($e->getMessage(), false);
        });
    }
}
