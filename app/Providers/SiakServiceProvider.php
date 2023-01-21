<?php

namespace App\Providers;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Browser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Siak\Tontine\Service\Charge\ChargeService;
use Siak\Tontine\Service\Charge\FeeService;
use Siak\Tontine\Service\Charge\FeeReportService;
use Siak\Tontine\Service\Charge\FineService;
use Siak\Tontine\Service\Charge\FineReportService;
use Siak\Tontine\Service\Charge\SettlementService;
use Siak\Tontine\Service\Meeting\DepositService;
use Siak\Tontine\Service\Meeting\LoanService;
use Siak\Tontine\Service\Meeting\PoolService as MeetingPoolService;
use Siak\Tontine\Service\Meeting\RefundService;
use Siak\Tontine\Service\Meeting\RemitmentService;
use Siak\Tontine\Service\Meeting\ReportService as MeetingReportService;
use Siak\Tontine\Service\Meeting\SessionService as MeetingSessionService;
use Siak\Tontine\Service\Planning\ReportService as PlanningReportService;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Planning\SessionService as PlanningSessionService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Report\PdfGeneratorInterface;
use Siak\Tontine\Service\Report\LocalPdfGenerator;
use Siak\Tontine\Service\Report\ReportServiceInterface;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\Tontine\PoolService as TontinePoolService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TenantService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\ChargeValidator;
use Siak\Tontine\Validation\MemberValidator;
use Siak\Tontine\Validation\Meeting\DebtValidator;
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
        $this->app->singleton(Browser::class, function() {
            $browserFactory = new BrowserFactory(config('chrome.binary'));
            // Starts headless chrome
            return $browserFactory->createBrowser(config('chrome.browser', []));
        });

        $this->app->singleton(ChargeService::class, ChargeService::class);
        $this->app->singleton(FeeService::class, FeeService::class);
        $this->app->singleton(FeeReportService::class, FeeReportService::class);
        $this->app->singleton(FineService::class, FineService::class);
        $this->app->singleton(FineReportService::class, FineReportService::class);
        $this->app->singleton(SettlementService::class, SettlementService::class);

        $this->app->singleton(LoanService::class, LoanService::class);
        $this->app->singleton(DepositService::class, DepositService::class);
        $this->app->singleton(MeetingPoolService::class, MeetingPoolService::class);
        $this->app->singleton(RefundService::class, RefundService::class);
        $this->app->singleton(RemitmentService::class, RemitmentService::class);
        $this->app->singleton(MeetingReportService::class, MeetingReportService::class);
        $this->app->singleton(MeetingSessionService::class, MeetingSessionService::class);

        $this->app->singleton(RoundService::class, RoundService::class);
        $this->app->singleton(PlanningSessionService::class, PlanningSessionService::class);
        $this->app->singleton(SubscriptionService::class, SubscriptionService::class);
        $this->app->singleton(PlanningReportService::class, PlanningReportService::class);

        $this->app->singleton(TenantService::class, TenantService::class);
        $this->app->singleton(TontinePoolService::class, TontinePoolService::class);
        $this->app->singleton(MemberService::class, MemberService::class);
        $this->app->singleton(TontineService::class, TontineService::class);

        $this->app->bind(PdfGeneratorInterface::class, LocalPdfGenerator::class);
        $this->app->singleton(LocalPdfGenerator::class, function($app) {
            return new LocalPdfGenerator($app->make(Browser::class), config('chrome.page'));
        });
        $this->app->singleton(ReportService::class, ReportService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);

        $this->app->singleton(ChargeValidator::class, ChargeValidator::class);
        $this->app->singleton(DebtValidator::class, DebtValidator::class);
        $this->app->singleton(LoanValidator::class, LoanValidator::class);
        $this->app->singleton(MemberValidator::class, MemberValidator::class);
        $this->app->singleton(PoolValidator::class, PoolValidator::class);
        $this->app->singleton(RemitmentValidator::class, RemitmentValidator::class);
        $this->app->singleton(SessionValidator::class, SessionValidator::class);
    }
}
