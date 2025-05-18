<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\LaravelLocalization;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\DataSyncService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Guild;
use Siak\Tontine\Service\Meeting;
use Siak\Tontine\Service\Payment;
use Siak\Tontine\Service\Planning;
use Siak\Tontine\Service\Presence;
use Siak\Tontine\Service\Report;
use Siak\Tontine\Validation;
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
    {}

    /**
     * Register any application services
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SqidsInterface::class, fn() => new Sqids(minLength: 8));

        $this->app->singleton(LocaleService::class, function($app) {
            $vendorDir = base_path('vendor');
            // Read country list from the umpirsky/country-list package data.
            $countriesDataDir = $vendorDir . '/umpirsky/country-list/data';
            // Read currency list from the umpirsky/currency-list package data.
            $currenciesDataDir = $vendorDir . '/umpirsky/currency-list/data';
            $localization = $app->make(LaravelLocalization::class);
            return new LocaleService($localization, $countriesDataDir, $currenciesDataDir);
        });
        $this->app->singleton(TenantService::class, TenantService::class);
        $this->app->singleton(BalanceCalculator::class, BalanceCalculator::class);
        $this->app->singleton(DataSyncService::class, DataSyncService::class);

        $this->app->singleton(Guild\FundService::class, Guild\FundService::class);
        $this->app->singleton(Guild\ChargeService::class, Guild\ChargeService::class);
        $this->app->singleton(Guild\AccountService::class, Guild\AccountService::class);
        $this->app->singleton(Guild\PoolService::class, Guild\PoolService::class);
        $this->app->singleton(Guild\RoundService::class, Guild\RoundService::class);
        $this->app->singleton(Guild\SessionService::class, Guild\SessionService::class);
        $this->app->singleton(Guild\UserService::class, Guild\UserService::class);
        $this->app->singleton(Guild\MemberService::class, Guild\MemberService::class);
        $this->app->singleton(Guild\GuildService::class, Guild\GuildService::class);

        $this->app->singleton(Planning\PoolService::class, Planning\PoolService::class);
        $this->app->singleton(Planning\FundService::class, Planning\FundService::class);
        $this->app->singleton(Planning\MemberService::class, Planning\MemberService::class);
        $this->app->singleton(Planning\ChargeService::class, Planning\ChargeService::class);
        $this->app->singleton(Planning\SubscriptionService::class, Planning\SubscriptionService::class);
        $this->app->singleton(Planning\SummaryService::class, Planning\SummaryService::class);

        $this->app->singleton(Meeting\Session\SummaryService::class, Meeting\Session\SummaryService::class);
        $this->app->singleton(Meeting\Session\SessionService::class, Meeting\Session\SessionService::class);

        $this->app->singleton(Meeting\Pool\PoolService::class, Meeting\Pool\PoolService::class);
        $this->app->singleton(Meeting\Pool\DepositService::class, Meeting\Pool\DepositService::class);
        $this->app->singleton(Meeting\Pool\RemitmentService::class, Meeting\Pool\RemitmentService::class);
        $this->app->singleton(Meeting\Pool\AuctionService::class, Meeting\Pool\AuctionService::class);

        $this->app->singleton(Meeting\Charge\ChargeService::class, Meeting\Charge\ChargeService::class);
        $this->app->singleton(Meeting\Charge\FixedFeeService::class, Meeting\Charge\FixedFeeService::class);
        $this->app->singleton(Meeting\Charge\LibreFeeService::class, Meeting\Charge\LibreFeeService::class);
        $this->app->singleton(Meeting\Charge\BillService::class, Meeting\Charge\BillService::class);
        $this->app->singleton(Meeting\Charge\SettlementService::class, Meeting\Charge\SettlementService::class);
        $this->app->singleton(Meeting\Charge\SettlementTargetService::class,
            Meeting\Charge\SettlementTargetService::class);

        $this->app->singleton(Meeting\Member\MemberService::class, Meeting\Member\MemberService::class);

        $this->app->singleton(Meeting\Saving\FundService::class, Meeting\Saving\FundService::class);
        $this->app->singleton(Meeting\Saving\SavingService::class, Meeting\Saving\SavingService::class);
        $this->app->singleton(Meeting\Saving\ProfitService::class, Meeting\Saving\ProfitService::class);

        $this->app->singleton(Meeting\Credit\DebtCalculator::class, Meeting\Credit\DebtCalculator::class);
        $this->app->singleton(Meeting\Credit\LoanService::class, Meeting\Credit\LoanService::class);
        $this->app->singleton(Meeting\Credit\RefundService::class, Meeting\Credit\RefundService::class);
        $this->app->singleton(Meeting\Credit\PartialRefundService::class,
            Meeting\Credit\PartialRefundService::class);

        $this->app->singleton(Meeting\Cash\OutflowService::class, Meeting\Cash\OutflowService::class);

        $this->app->singleton(Presence\PresenceService::class, Presence\PresenceService::class);

        $this->app->singleton(Report\MemberService::class, Report\MemberService::class);
        $this->app->singleton(Report\RoundService::class, Report\RoundService::class);
        $this->app->singleton(Report\SessionService::class, Report\SessionService::class);
        $this->app->singleton(Report\ReportService::class, Report\ReportService::class);
        $this->app->singleton(Report\Pdf\PdfPrinterService::class, Report\Pdf\PdfPrinterService::class);
        $this->app->when(Report\Pdf\PdfPrinterService::class)
            ->needs('$config')
            ->give(config('chrome.page'));

        $this->app->singleton(Payment\PaymentService::class, Payment\PaymentService::class);
        $this->app->singleton(Payment\PaymentServiceInterface::class, function() {
            return new class implements Payment\PaymentServiceInterface {
                // By default, all the payment items are editable.
                public function isEditable(Model $_): bool
                {
                    return true;
                }
            };
        });

        $this->app->singleton(Validation\Guild\ChargeValidator::class,
            Validation\Guild\ChargeValidator::class);
        $this->app->singleton(Validation\Guild\FundValidator::class,
            Validation\Guild\FundValidator::class);
        $this->app->singleton(Validation\Guild\MemberValidator::class,
            Validation\Guild\MemberValidator::class);
        $this->app->singleton(Validation\Guild\RoundValidator::class,
            Validation\Guild\RoundValidator::class);
        $this->app->singleton(Validation\Guild\PoolValidator::class,
            Validation\Guild\PoolValidator::class);
        $this->app->singleton(Validation\Guild\SessionValidator::class,
            Validation\Guild\SessionValidator::class);
        $this->app->singleton(Validation\Guild\OptionsValidator::class,
            Validation\Guild\OptionsValidator::class);
        $this->app->singleton(Validation\Guild\GuildValidator::class,
            Validation\Guild\GuildValidator::class);
        $this->app->singleton(Validation\Guild\HostAccessValidator::class,
            Validation\Guild\HostAccessValidator::class);
        $this->app->singleton(Validation\Guild\GuestInviteValidator::class,
            Validation\Guild\GuestInviteValidator::class);

        $this->app->singleton(Validation\Meeting\DebtValidator::class,
            Validation\Meeting\DebtValidator::class);
        $this->app->singleton(Validation\Meeting\SavingValidator::class,
            Validation\Meeting\SavingValidator::class);
        $this->app->singleton(Validation\Meeting\OutflowValidator::class,
            Validation\Meeting\OutflowValidator::class);
        $this->app->singleton(Validation\Meeting\LoanValidator::class,
            Validation\Meeting\LoanValidator::class);
        $this->app->singleton(Validation\Meeting\RemitmentValidator::class,
            Validation\Meeting\RemitmentValidator::class);
        $this->app->singleton(Validation\Meeting\TargetValidator::class,
            Validation\Meeting\TargetValidator::class);
        $this->app->singleton(Validation\SearchSanitizer::class,
            Validation\SearchSanitizer::class);

        $this->app->singleton(Validation\Planning\FundSessionsValidator::class,
            Validation\Planning\FundSessionsValidator::class);
        $this->app->singleton(Validation\Planning\PoolSessionsValidator::class,
            Validation\Planning\PoolSessionsValidator::class);
    }
}
