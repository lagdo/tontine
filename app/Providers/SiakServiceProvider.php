<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService as MeetingPoolService;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Meeting\SessionService as MeetingSessionService;
use Siak\Tontine\Service\Meeting\SummaryService as MeetingSummaryService;
use Siak\Tontine\Service\Planning\PoolService as TontinePoolService;
use Siak\Tontine\Service\Planning\RoundService;
use Siak\Tontine\Service\Planning\SessionService as PlanningSessionService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService as PlanningSummaryService;
use Siak\Tontine\Service\Report\MemberService as MemberReportService;
use Siak\Tontine\Service\Report\Pdf\PrinterService;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\Report\RoundService as RoundReportService;
use Siak\Tontine\Service\Report\SessionService as SessionReportService;
use Siak\Tontine\Service\Tontine\CategoryService;
use Siak\Tontine\Service\Tontine\ChargeService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Service\Tontine\TontineService;
use Siak\Tontine\Validation\Meeting\ClosingValidator;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Siak\Tontine\Validation\Meeting\LoanValidator;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;
use Siak\Tontine\Validation\Meeting\SavingValidator;
use Siak\Tontine\Validation\Meeting\TargetValidator;
use Siak\Tontine\Validation\Planning\PoolValidator;
use Siak\Tontine\Validation\Planning\PoolRoundValidator;
use Siak\Tontine\Validation\Planning\RoundValidator;
use Siak\Tontine\Validation\Planning\SessionValidator;
use Siak\Tontine\Validation\Tontine\ChargeValidator;
use Siak\Tontine\Validation\Tontine\MemberValidator;
use Siak\Tontine\Validation\Tontine\OptionsValidator;
use Siak\Tontine\Validation\Tontine\TontineValidator;
use Sqids\Sqids;
use Sqids\SqidsInterface;

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
        $this->app->singleton(SqidsInterface::class, function() {
            return new Sqids(minLength: 8);
        });

        $this->app->singleton(FundService::class, FundService::class);
        $this->app->singleton(ChargeService::class, ChargeService::class);
        $this->app->singleton(CategoryService::class, CategoryService::class);
        $this->app->singleton(FixedFeeService::class, FixedFeeService::class);
        $this->app->singleton(LibreFeeService::class, LibreFeeService::class);
        $this->app->singleton(BillService::class, BillService::class);
        $this->app->singleton(SettlementService::class, SettlementService::class);
        $this->app->singleton(SettlementTargetService::class, SettlementTargetService::class);

        $this->app->singleton(SavingService::class, SavingService::class);
        $this->app->singleton(DebtCalculator::class, DebtCalculator::class);
        $this->app->singleton(LoanService::class, LoanService::class);
        $this->app->singleton(AuctionService::class, AuctionService::class);
        $this->app->singleton(DepositService::class, DepositService::class);
        $this->app->singleton(BalanceCalculator::class, BalanceCalculator::class);
        $this->app->singleton(DisbursementService::class, DisbursementService::class);
        $this->app->singleton(MeetingPoolService::class, MeetingPoolService::class);
        $this->app->singleton(RefundService::class, RefundService::class);
        $this->app->singleton(ProfitService::class, ProfitService::class);
        $this->app->singleton(RemitmentService::class, RemitmentService::class);
        $this->app->singleton(MeetingSummaryService::class, MeetingSummaryService::class);
        $this->app->singleton(MeetingSessionService::class, MeetingSessionService::class);
        $this->app->singleton(MemberReportService::class, MemberReportService::class);
        $this->app->singleton(RoundReportService::class, RoundReportService::class);
        $this->app->singleton(SessionReportService::class, SessionReportService::class);
        $this->app->singleton(ReportService::class, ReportService::class);
        $this->app->singleton(PrinterService::class, PrinterService::class);
        $this->app->when(PrinterService::class)
            ->needs('$config')
            ->give(config('chrome.page'));

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
        $this->app->singleton(SavingValidator::class, SavingValidator::class);
        $this->app->singleton(ClosingValidator::class, ClosingValidator::class);
        $this->app->singleton(DisbursementValidator::class, DisbursementValidator::class);
        $this->app->singleton(LoanValidator::class, LoanValidator::class);
        $this->app->singleton(MemberValidator::class, MemberValidator::class);
        $this->app->singleton(RoundValidator::class, RoundValidator::class);
        $this->app->singleton(PoolValidator::class, PoolValidator::class);
        $this->app->singleton(PoolRoundValidator::class, PoolRoundValidator::class);
        $this->app->singleton(RemitmentValidator::class, RemitmentValidator::class);
        $this->app->singleton(SessionValidator::class, SessionValidator::class);
        $this->app->singleton(OptionsValidator::class, OptionsValidator::class);
        $this->app->singleton(TontineValidator::class, TontineValidator::class);
        $this->app->singleton(TargetValidator::class, TargetValidator::class);
    }
}
