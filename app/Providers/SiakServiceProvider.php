<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\LaravelLocalization;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Guild\AccountService;
use Siak\Tontine\Service\Guild\ChargeService;
use Siak\Tontine\Service\Guild\DataSyncService;
use Siak\Tontine\Service\Guild\FundService;
use Siak\Tontine\Service\Guild\GuildService;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Service\Guild\PoolService;
use Siak\Tontine\Service\Guild\RoundService;
use Siak\Tontine\Service\Guild\SessionService as GuildSessionService;
use Siak\Tontine\Service\Guild\UserService;
use Siak\Tontine\Service\Meeting\Cash\DisbursementService;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Meeting\Charge\SettlementTargetService;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;
use Siak\Tontine\Service\Meeting\Credit\LoanService;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Meeting\PaymentServiceInterface;
use Siak\Tontine\Service\Meeting\PaymentService;
use Siak\Tontine\Service\Meeting\Pool\AuctionService;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService as MeetingPoolService;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Siak\Tontine\Service\Meeting\PresenceService;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Meeting\SessionService as MeetingSessionService;
use Siak\Tontine\Service\Meeting\SummaryService as MeetingSummaryService;
use Siak\Tontine\Service\Planning\PoolService as PlanningPoolService;
use Siak\Tontine\Service\Planning\SubscriptionService;
use Siak\Tontine\Service\Planning\SummaryService as PlanningSummaryService;
use Siak\Tontine\Service\Report\MemberService as MemberReportService;
use Siak\Tontine\Service\Report\Pdf\PdfPrinterService;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\Report\RoundService as RoundReportService;
use Siak\Tontine\Service\Report\SessionService as SessionReportService;
use Siak\Tontine\Validation\Guild\ChargeValidator;
use Siak\Tontine\Validation\Guild\FundValidator;
use Siak\Tontine\Validation\Guild\GuestInviteValidator;
use Siak\Tontine\Validation\Guild\GuildValidator;
use Siak\Tontine\Validation\Guild\HostAccessValidator;
use Siak\Tontine\Validation\Guild\MemberValidator;
use Siak\Tontine\Validation\Guild\OptionsValidator;
use Siak\Tontine\Validation\Guild\PoolValidator;
use Siak\Tontine\Validation\Guild\RoundValidator;
use Siak\Tontine\Validation\Guild\SessionValidator;
use Siak\Tontine\Validation\Meeting\ClosingValidator;
use Siak\Tontine\Validation\Meeting\DisbursementValidator;
use Siak\Tontine\Validation\Meeting\DebtValidator;
use Siak\Tontine\Validation\Meeting\LoanValidator;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;
use Siak\Tontine\Validation\Meeting\SavingValidator;
use Siak\Tontine\Validation\Meeting\TargetValidator;
use Siak\Tontine\Validation\Planning\SessionsValidator;
use Sqids\Sqids;
use Sqids\SqidsInterface;

use function base_path;
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
        $this->app->singleton(LocaleService::class, function($app) {
            $vendorDir = base_path('vendor');
            // Read country list from the umpirsky/country-list package data.
            $countriesDataDir = $vendorDir . '/umpirsky/country-list/data';
            // Read currency list from the umpirsky/currency-list package data.
            $currenciesDataDir = $vendorDir . '/umpirsky/currency-list/data';
            $localization = $app->make(LaravelLocalization::class);
            return new LocaleService($localization, $countriesDataDir, $currenciesDataDir);
        });
        $this->app->singleton(SqidsInterface::class, function() {
            return new Sqids(minLength: 8);
        });

        $this->app->singleton(FundService::class, FundService::class);
        $this->app->singleton(ChargeService::class, ChargeService::class);
        $this->app->singleton(AccountService::class, AccountService::class);
        $this->app->singleton(FixedFeeService::class, FixedFeeService::class);
        $this->app->singleton(LibreFeeService::class, LibreFeeService::class);
        $this->app->singleton(BillService::class, BillService::class);
        $this->app->singleton(SettlementService::class, SettlementService::class);
        $this->app->singleton(SettlementTargetService::class, SettlementTargetService::class);
        $this->app->singleton(PoolService::class, PoolService::class);

        $this->app->singleton(ClosingService::class, ClosingService::class);
        $this->app->singleton(SavingService::class, SavingService::class);
        $this->app->singleton(DebtCalculator::class, DebtCalculator::class);
        $this->app->singleton(LoanService::class, LoanService::class);
        $this->app->singleton(AuctionService::class, AuctionService::class);
        $this->app->singleton(DepositService::class, DepositService::class);
        $this->app->singleton(BalanceCalculator::class, BalanceCalculator::class);
        $this->app->singleton(DisbursementService::class, DisbursementService::class);
        $this->app->singleton(MeetingPoolService::class, MeetingPoolService::class);
        $this->app->singleton(RefundService::class, RefundService::class);
        $this->app->singleton(PartialRefundService::class, PartialRefundService::class);
        $this->app->singleton(ProfitService::class, ProfitService::class);
        $this->app->singleton(RemitmentService::class, RemitmentService::class);
        $this->app->singleton(MeetingSummaryService::class, MeetingSummaryService::class);
        $this->app->singleton(MeetingSessionService::class, MeetingSessionService::class);
        $this->app->singleton(PresenceService::class, PresenceService::class);
        $this->app->singleton(MemberReportService::class, MemberReportService::class);
        $this->app->singleton(RoundReportService::class, RoundReportService::class);
        $this->app->singleton(SessionReportService::class, SessionReportService::class);
        $this->app->singleton(ReportService::class, ReportService::class);
        $this->app->singleton(PdfPrinterService::class, PdfPrinterService::class);
        $this->app->when(PdfPrinterService::class)
            ->needs('$config')
            ->give(config('chrome.page'));

        $this->app->singleton(RoundService::class, RoundService::class);
        $this->app->singleton(GuildSessionService::class, GuildSessionService::class);
        $this->app->singleton(SubscriptionService::class, SubscriptionService::class);
        $this->app->singleton(PlanningSummaryService::class, PlanningSummaryService::class);
        $this->app->singleton(DataSyncService::class, DataSyncService::class);

        $this->app->singleton(TenantService::class, TenantService::class);
        $this->app->singleton(PlanningPoolService::class, PlanningPoolService::class);
        $this->app->singleton(UserService::class, UserService::class);
        $this->app->singleton(MemberService::class, MemberService::class);
        $this->app->singleton(GuildService::class, GuildService::class);

        $this->app->singleton(PaymentService::class, PaymentService::class);
        $this->app->singleton(PaymentServiceInterface::class, function() {
            return new class implements PaymentServiceInterface {
                // By default, all the payment items are editable.
                public function isEditable(Model $_): bool
                {
                    return true;
                }
            };
        });

        $this->app->singleton(ChargeValidator::class, ChargeValidator::class);
        $this->app->singleton(DebtValidator::class, DebtValidator::class);
        $this->app->singleton(FundValidator::class, FundValidator::class);
        $this->app->singleton(SavingValidator::class, SavingValidator::class);
        $this->app->singleton(ClosingValidator::class, ClosingValidator::class);
        $this->app->singleton(DisbursementValidator::class, DisbursementValidator::class);
        $this->app->singleton(LoanValidator::class, LoanValidator::class);
        $this->app->singleton(MemberValidator::class, MemberValidator::class);
        $this->app->singleton(RoundValidator::class, RoundValidator::class);
        $this->app->singleton(PoolValidator::class, PoolValidator::class);
        $this->app->singleton(SessionsValidator::class, SessionsValidator::class);
        $this->app->singleton(RemitmentValidator::class, RemitmentValidator::class);
        $this->app->singleton(SessionValidator::class, SessionValidator::class);
        $this->app->singleton(OptionsValidator::class, OptionsValidator::class);
        $this->app->singleton(GuildValidator::class, GuildValidator::class);
        $this->app->singleton(TargetValidator::class, TargetValidator::class);
        $this->app->singleton(HostAccessValidator::class, HostAccessValidator::class);
        $this->app->singleton(GuestInviteValidator::class, GuestInviteValidator::class);
    }
}
