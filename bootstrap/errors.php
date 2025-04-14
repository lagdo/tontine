<?php

namespace App\Exceptions;

use Ajax\App\Admin\Guild\Guild;
use Ajax\App\Guild\Calendar\Round;
use Ajax\App\Guild\Member\Member;
use Ajax\App\Meeting\Session\Session;
use Ajax\App\Planning\Financial\Pool;
use Illuminate\Foundation\Configuration\Exceptions;
use Jaxon\Laravel\App\Jaxon;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Exception\MeetingRoundException;
use Siak\Tontine\Exception\PlanningPoolException;
use Siak\Tontine\Exception\PlanningRoundException;
use Siak\Tontine\Exception\TontineMemberException;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function app;
use function Jaxon\cl;
use function route;
use function trans;

/**
 * Check guest user access before redirection
 *
 * @param string $section
 * @param string $entry
 *
 * @return bool
 */
function checkHostAccess(string $section, string $entry): bool
{
    /** @var TenantService */
    $tenantService = app()->make(TenantService::class);
    if(!($access = $tenantService->checkHostAccess($section, $entry, true)))
    {
        cl(Guild::class)->home();
    }
    return $access;
}

/**
 * @param string $message
 * @param bool $isError
 *
 * @return Response
 */
function showMessage(string $message, bool $isError): Response
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


function handle(Exceptions $exceptions)
{
    $exceptions->dontReport([
        MessageException::class,
        ValidationException::class,
        PlanningPoolException::class,
        PlanningRoundException::class,
        TontineMemberException::class,
        MeetingRoundException::class,
    ]);

    $exceptions->report(function (Throwable $e) {
        //
    });

    $exceptions->respond(function (Response $response) {
        /** @var Jaxon */
        $jaxon = app()->make(Jaxon::class);
        if ($response->getStatusCode() !== 419 || !$jaxon->canProcessRequest()) {
            return $response;
        }
 
        // Handle token expiration errors on Jaxon requests.
        $ajaxResponse = $jaxon->ajaxResponse();
        $ajaxResponse->redirect(route('login'));

        return $jaxon->httpResponse();
    });

    // Show the error message in a dialog
    $exceptions->render(function (MessageException $e) {
        return showMessage($e->getMessage(), $e->isError);
    });

    // Show the error message in a dialog
    $exceptions->render(function (ValidationException $e) {
        return showMessage($e->getMessage(), true);
    });

    // Show the warning message in a dialog, and show the sessions page.
    $exceptions->render(function (PlanningRoundException $e) {
        if(checkHostAccess('planning', 'sessions'))
        {
            cl(Round::class)->home();
        }

        return showMessage($e->getMessage(), false);
    });

    // Show the warning message in a dialog, and show the pools page.
    $exceptions->render(function (PlanningPoolException $e) {
        if(checkHostAccess('planning', 'pools'))
        {
            cl(Pool::class)->home();
        }

        return showMessage($e->getMessage(), false);
    });

    // Show the warning message in a dialog, and show the members page.
    $exceptions->render(function (TontineMemberException $e) {
        if(checkHostAccess('tontine', 'members'))
        {
            cl(Member::class)->home();
        }

        return showMessage($e->getMessage(), false);
    });

    // Show the warning message in a dialog, and show the sessions page.
    $exceptions->render(function (MeetingRoundException $e) {
        if(checkHostAccess('meeting', 'sessions'))
        {
            cl(Session::class)->home();
        }

        return showMessage($e->getMessage(), false);
    });
}
