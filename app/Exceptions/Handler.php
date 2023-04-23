<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jaxon\Laravel\Jaxon;
use Siak\Tontine\Exception\MessageException;
use Throwable;

use function app;
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
        MessageException::class,
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
        $this->reportable(function (Throwable $e) {
            //
        });

        // Show the error message in a dialog
        $this->renderable(function (MessageException $e) {
            $jaxon = app()->make(Jaxon::class);
            $ajaxResponse = $jaxon->ajaxResponse();
            $ajaxResponse->clearCommands();
            $ajaxResponse->dialog->error($e->getMessage(), trans('common.titles.error'));

            return $jaxon->httpResponse();
        });
    }
}
