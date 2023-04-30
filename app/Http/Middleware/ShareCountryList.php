<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Siak\Tontine\Service\LocaleService;

use function view;

class ShareCountryList
{
    /**
     * @var LocaleService
     */
    private LocaleService $localeService;

    /**
     * @param LocaleService $localeService
     */
    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        view()->share(['countries' => $this->localeService->getCountries()]);

        return $next($request);
    }
}
