<?php

namespace App\Providers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Browser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\ChargeService;
use Siak\Tontine\Service\FundService;
use Siak\Tontine\Service\SubscriptionService;
use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Service\MemberService;
use Siak\Tontine\Service\SessionService;
use Siak\Tontine\Service\DepositService;
use Siak\Tontine\Service\RemittanceService;
use Siak\Tontine\Service\RoundService;
use Siak\Tontine\Service\TontineService;
use Siak\Tontine\Service\FeeSettlementService;
use Siak\Tontine\Service\FineSettlementService;
use Siak\Tontine\Service\BiddingService;
use Siak\Tontine\Service\RefundService;
use Siak\Tontine\Service\PlanningService;
use Siak\Tontine\Validation\ChargeValidator;
use Siak\Tontine\Validation\MemberValidator;
use Siak\Tontine\Validation\Meeting\BiddingValidator;
use Siak\Tontine\Validation\Meeting\RemittanceValidator;
use Siak\Tontine\Validation\Planning\FundValidator;
use Siak\Tontine\Validation\Planning\SessionValidator;
use Siak\Tontine\Service\Report\PdfGeneratorInterface;
use Siak\Tontine\Service\Report\LocalPdfGenerator;
use Siak\Tontine\Service\Report\ReportServiceInterface;
use Siak\Tontine\Service\Report\ReportService;

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
        $this->app->singleton(FundService::class, FundService::class);
        $this->app->singleton(ChargeService::class, ChargeService::class);
        $this->app->singleton(SubscriptionService::class, SubscriptionService::class);
        $this->app->singleton(MemberService::class, MemberService::class);
        $this->app->singleton(MeetingService::class, MeetingService::class);
        $this->app->singleton(SessionService::class, SessionService::class);
        $this->app->singleton(DepositService::class, DepositService::class);
        $this->app->singleton(RemittanceService::class, RemittanceService::class);
        $this->app->singleton(RoundService::class, RoundService::class);
        $this->app->singleton(TontineService::class, TontineService::class);
        $this->app->singleton(FeeSettlementService::class, FeeSettlementService::class);
        $this->app->singleton(FineSettlementService::class, FineSettlementService::class);
        $this->app->singleton(BiddingService::class, BiddingService::class);
        $this->app->singleton(RefundService::class, RefundService::class);
        $this->app->singleton(PlanningService::class, PlanningService::class);
        $this->app->singleton(ReportService::class, ReportService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
        $this->app->singleton(LocalPdfGenerator::class, function($app) {
            return new LocalPdfGenerator($app->make(Browser::class), config('chrome.page'));
        });
        $this->app->bind(PdfGeneratorInterface::class, LocalPdfGenerator::class);

        $this->app->singleton(ChargeValidator::class, ChargeValidator::class);
        $this->app->singleton(MemberValidator::class, MemberValidator::class);
        $this->app->singleton(BiddingValidator::class, BiddingValidator::class);
        $this->app->singleton(FundValidator::class, FundValidator::class);
        $this->app->singleton(RemittanceValidator::class, RemittanceValidator::class);
        $this->app->singleton(SessionValidator::class, SessionValidator::class);

        $this->app->singleton(Browser::class, function() {
            $browserFactory = new BrowserFactory(config('chrome.binary'));
            // Starts headless chrome
            return $browserFactory->createBrowser(config('chrome.browser', []));
        });
    }
}
