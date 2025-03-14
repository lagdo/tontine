<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;

use function trans;

class LoanService
{
    /**
     * @param BalanceCalculator $balanceCalculator
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param FundService $fundService
     * @param MemberService $memberService
     */
    public function __construct(private BalanceCalculator $balanceCalculator,
        private LocaleService $localeService, private TenantService $tenantService,
        private FundService $fundService, private  MemberService $memberService)
    {}

    /**
     * @return array
     */
    public function getInterestTypes(): array
    {
        return [
            Loan::INTEREST_FIXED => trans('meeting.loan.interest.if'),
            Loan::INTEREST_UNIQUE => trans('meeting.loan.interest.iu'),
            Loan::INTEREST_SIMPLE => trans('meeting.loan.interest.is'),
            Loan::INTEREST_COMPOUND => trans('meeting.loan.interest.ic'),
        ];
    }

    /**
     * Get the loans for a given session.
     *
     * @param Session $session    The session
     * @param int $page
     *
     * @return Collection
     */
    public function getLoans(Session $session, int $page = 0): Collection
    {
        return $session->loans()
            ->with(['member', 'principal_debt', 'interest_debt', 'fund'])
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the amount available for loan.
     *
     * @param Session $session    The session
     *
     * @return int
     */
    public function getAmountAvailable(Session $session): int
    {
        return $this->balanceCalculator->getBalanceForLoan($session);
    }

    /**
     * Get the amount available for loan.
     *
     * @param Session $session    The session
     *
     * @return float
     */
    public function getAmountAvailableValue(Session $session): float
    {
        return $this->localeService->getMoneyValue($this->getAmountAvailable($session));
    }

    /**
     * Get the amount available for loan.
     *
     * @param Session $session    The session
     *
     * @return string
     */
    public function getFormattedAmountAvailable(Session $session): string
    {
        return $this->localeService->formatMoney($this->getAmountAvailable($session));
    }

    /**
     * Get the loans for a given session.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getSessionLoans(Session $session): Collection
    {
        return $session->loans()->with(['member', 'fund'])->get();
    }

    /**
     * Get a loan for a given session.
     *
     * @param Session $session
     * @param int $loanId
     *
     * @return Loan|null
     */
    public function getSessionLoan(Session $session, int $loanId): ?Loan
    {
        return $session->loans()->with(['member', 'fund'])->withCount('refunds')->find($loanId);
    }

    /**
     * Create a loan.
     *
     * @param Session $session The session
     * @param Member $member
     * @param Fund $fund
     * @param array $values
     *
     * @return void
     */
    public function createLoan(Session $session, Member $member, Fund $fund, array $values): void
    {
        $loan = new Loan();
        $loan->interest_type = $values['interest_type'];
        $loan->interest_rate = $values['interest_rate'];
        $loan->session()->associate($session);
        $loan->member()->associate($member);
        $loan->fund()->associate($fund);
        DB::transaction(function() use($loan, $values) {
            $loan->save();

            $principal = $values['principal'];
            $interest = $values['interest'];
            // Create an entry for each type of debt
            if($principal > 0)
            {
                $loan->debts()->create(['type' => Debt::TYPE_PRINCIPAL, 'amount' => $principal]);
            }
            if($interest > 0)
            {
                $loan->debts()->create(['type' => Debt::TYPE_INTEREST, 'amount' => $interest]);
            }
        });
    }

    /**
     * Update a loan.
     *
     * @param Member $member
     * @param Fund $fund
     * @param Loan $loan
     * @param array $values
     *
     * @return void
     */
    public function updateLoan(Member $member, Fund $fund, Loan $loan, array $values): void
    {
        $loan->interest_type = $values['interest_type'];
        $loan->interest_rate = $values['interest_rate'];
        $loan->member()->associate($member);
        $loan->fund()->associate($fund);
        DB::transaction(function() use($loan, $values) {
            $loan->save();

            $principal = $values['principal'];
            $interest = $values['interest'];
            $loan->debts()->principal()->update(['amount' => $principal]);
            // The interest debt may need to be created or deleted.
            if($interest <= 0)
            {
                $loan->debts()->where('type', Debt::TYPE_INTEREST)->delete();
                return;
            }
            $loan->debts()->updateOrCreate(['type' => Debt::TYPE_INTEREST], ['amount' => $interest]);
        });
    }

    /**
     * Delete a loan.
     *
     * @param Session $session The session
     * @param int $loanId
     *
     * @return void
     */
    public function deleteLoan(Session $session, int $loanId): void
    {
        /** @var Loan|null */
        $loan = $session->loans()->find($loanId);
        if($loan !== null)
        {
            DB::transaction(function() use($loan) {
                $loan->refunds()->delete();
                $loan->debts()->delete();
                $loan->delete();
            });
        }
    }
}
