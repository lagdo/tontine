<?php

namespace App\Providers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Browser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\ChargeService;
use Siak\Tontine\Service\PoolService;
use Siak\Tontine\Service\SubscriptionService;
use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Service\MemberService;
use Siak\Tontine\Service\SessionService;
use Siak\Tontine\Service\DepositService;
use Siak\Tontine\Service\RemitmentService;
use Siak\Tontine\Service\RoundService;
use Siak\Tontine\Service\TontineService;
use Siak\Tontine\Service\FeeSettlementService;
use Siak\Tontine\Service\FineSettlementService;
use Siak\Tontine\Service\LoanService;
use Siak\Tontine\Service\RefundService;
use Siak\Tontine\Service\PlanningService;
use Siak\Tontine\Validation\ChargeValidator;
use Siak\Tontine\Validation\MemberValidator;
use Siak\Tontine\Validation\Meeting\LoanValidator;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;
use Siak\Tontine\Validation\Planning\PoolValidator;
use Siak\Tontine\Validation\Planning\SessionValidator;

use function config;

class SiakServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Log database queries
        // DB::listen(function($query) {
        //     Log::info($query->sql, $query->bindings, $query->time);
        // });
    }

    /**
     * Register any application services
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TenantService::class, TenantService::class);
        $this->app->singleton(PoolService::class, PoolService::class);
        $this->app->singleton(ChargeService::class, ChargeService::class);
        $this->app->singleton(SubscriptionService::class, SubscriptionService::class);
        $this->app->singleton(MemberService::class, MemberService::class);
        $this->app->singleton(MeetingService::class, MeetingService::class);
        $this->app->singleton(SessionService::class, SessionService::class);
        $this->app->singleton(DepositService::class, DepositService::class);
        $this->app->singleton(RemitmentService::class, RemitmentService::class);
        $this->app->singleton(RoundService::class, RoundService::class);
        $this->app->singleton(TontineService::class, TontineService::class);
        $this->app->singleton(FeeSettlementService::class, FeeSettlementService::class);
        $this->app->singleton(FineSettlementService::class, FineSettlementService::class);
        $this->app->singleton(LoanService::class, LoanService::class);
        $this->app->singleton(RefundService::class, RefundService::class);
        $this->app->singleton(PlanningService::class, PlanningService::class);
        $this->app->singleton(ChargeValidator::class, ChargeValidator::class);
        $this->app->singleton(MemberValidator::class, MemberValidator::class);
        $this->app->singleton(LoanValidator::class, LoanValidator::class);
        $this->app->singleton(PoolValidator::class, PoolValidator::class);
        $this->app->singleton(RemitmentValidator::class, RemitmentValidator::class);
        $this->app->singleton(SessionValidator::class, SessionValidator::class);

        $this->app->singleton(Browser::class, function() {
            $browserFactory = new BrowserFactory(config('chrome.binary'));
            // Starts headless chrome
            return $browserFactory->createBrowser(config('chrome.browser', []));
        });
    }
}
