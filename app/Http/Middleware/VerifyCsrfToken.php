<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\App;
use Jaxon\Laravel\Jaxon;
use Siak\Tontine\Exception\AuthenticationException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws AuthenticationException
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        try
        {
            return parent::handle($request, $next);
        }
        catch(TokenMismatchException $e)
        {
            $jaxon = App::make(Jaxon::class);
            if($jaxon->canProcessRequest()) // We have an ajax request with Jaxon
            {
                throw new AuthenticationException();
            }

            throw $e;
        }
    }
}
