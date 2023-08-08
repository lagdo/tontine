<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Charge\FeeService;
use Siak\Tontine\Service\Meeting\Charge\FineService;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Meeting\Credit\DisbursementService;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Meeting\Credit\FundingService;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService as MeetingPoolService;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Siak\Tontine\Service\Meeting\SessionService as MeetingSessionService;
use Siak\Tontine\Service\Meeting\SummaryService as MeetingSummaryService;
use Siak\Tontine\Service\Planning\PoolService as TontinePoolService;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Planning\SessionService as PlanningSessionService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService as PlanningSummaryService;
use Siak\Tontine\Service\Report\MemberService as MemberReportService;
use Siak\Tontine\Service\Report\RoundService as RoundReportService;
use Siak\Tontine\Service\Report\SessionService as SessionReportService;
use Siak\Tontine\Service\Tontine\ChargeService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Siak\Tontine\Validation\Meeting\FundingValidator;
use Siak\Tontine\Validation\Meeting\LoanValidator;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;
use Siak\Tontine\Validation\Planning\PoolValidator;
use Siak\Tontine\Validation\Planning\SessionValidator;
use Siak\Tontine\Validation\Tontine\ChargeValidator;
use Siak\Tontine\Validation\Tontine\MemberValidator;
use Siak\Tontine\Validation\Tontine\TontineValidator;

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
        $this->app->singleton(LocaleService::class, function() {
            $locale = LaravelLocalization::getCurrentLocale();
            // Vendor dir
            $vendorDir = __DIR__ . '/../../vendor';
            // Read country list from the umpirsky/country-list package data.
            $countriesDataDir = $vendorDir . '/umpirsky/country-list/data';
            // Read currency list from the umpirsky/currency-list package data.
            $currenciesDataDir = $vendorDir . '/umpirsky/currency-list/data';

            return new LocaleService($locale, $countriesDataDir, $currenciesDataDir);
        });

        $this->app->singleton(ChargeService::class, ChargeService::class);
        $this->app->singleton(FeeService::class, FeeService::class);
        $this->app->singleton(FineService::class, FineService::class);
        $this->app->singleton(BillService::class, BillService::class);
        $this->app->singleton(SettlementService::class, SettlementService::class);

        $this->app->singleton(FundingService::class, FundingService::class);
        $this->app->singleton(LoanService::class, LoanService::class);
        $this->app->singleton(DepositService::class, DepositService::class);
        $this->app->singleton(DisbursementService::class, DisbursementService::class);
        $this->app->singleton(MeetingPoolService::class, MeetingPoolService::class);
        $this->app->singleton(RefundService::class, RefundService::class);
        $this->app->singleton(RemitmentService::class, RemitmentService::class);
        $this->app->singleton(MeetingSummaryService::class, MeetingSummaryService::class);
        $this->app->singleton(MeetingSessionService::class, MeetingSessionService::class);
        $this->app->singleton(MemberReportService::class, MemberReportService::class);
        $this->app->singleton(RoundReportService::class, RoundReportService::class);
        $this->app->singleton(SessionReportService::class, SessionReportService::class);

        $this->app->singleton(RoundService::class, RoundService::class);
        $this->app->singleton(PlanningSessionService::class, PlanningSessionService::class);
        $this->app->singleton(SubscriptionService::class, SubscriptionService::class);
        $this->app->singleton(PlanningSummaryService::class, PlanningSummaryService::class);

        $this->app->singleton(TenantService::class, TenantService::class);
        $this->app->singleton(TontinePoolService::class, TontinePoolService::class);
        $this->app->singleton(MemberService::class, MemberService::class);
        $this->app->singleton(TontineService::class, TontineService::class);

        $this->app->singleton(ChargeValidator::class, ChargeValidator::class);
        $this->app->singleton(DebtValidator::class, DebtValidator::class);
        $this->app->singleton(FundingValidator::class, FundingValidator::class);
        $this->app->singleton(DisbursementValidator::class, DisbursementValidator::class);
        $this->app->singleton(LoanValidator::class, LoanValidator::class);
        $this->app->singleton(MemberValidator::class, MemberValidator::class);
        $this->app->singleton(PoolValidator::class, PoolValidator::class);
        $this->app->singleton(RemitmentValidator::class, RemitmentValidator::class);
        $this->app->singleton(SessionValidator::class, SessionValidator::class);
        $this->app->singleton(TontineValidator::class, TontineValidator::class);
    }
}
