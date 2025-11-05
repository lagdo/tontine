<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

use Ajax\App\Meeting\Session\Charge\FuncComponent;
use Jaxon\Attributes\Attribute\Before;
use Siak\Tontine\Service\LocaleService;

class AmountFunc extends FuncComponent
{
    use AmountTrait;

    /**
     * The constructor
     *
     * @param LocaleService $localeService
     */
    public function __construct(protected LocaleService $localeService)
    {}

    /**
     * @param int $memberId
     *
     * @return void
     */
    private function refresh(int $memberId): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $member = $this->billService->getMember($charge, $session, $memberId);

        $this->stash()->set('meeting.charge.member', $member);
        $this->stash()->set('meeting.charge.bill', $member?->bill);

        $this->cl(MemberName::class)->item($memberId)->render();
        $this->cl(Amount::class)->item($memberId)->render();
    }

    /**
     * @param int $memberId
     *
     * @return void
     */
    #[Before('checkChargeEdit')]
    public function edit(int $memberId): void
    {
        $this->stash()->set('meeting.charge.edit', true);

        $this->refresh($memberId);
    }

    /**
     * @param int $memberId
     * @param bool $paid
     * @param string $amount
     *
     * @return void
     */
    private function saveAmount(int $memberId, bool $paid, string $amount): void
    {
        $session = $this->stash()->get('meeting.session');
        $charge = $this->stash()->get('meeting.session.charge');
        $amount = $this->convertAmount($amount);

        if(!$amount)
        {
            // No amount provided => the bill is deleted.
            $this->billService->deleteBill($charge, $session, $memberId);
            return;
        }

        $bill = $this->billService->getMemberBill($charge, $session, $memberId);
        if($bill !== null)
        {
            // The bill exists => it is updated.
            $this->billService->updateBill($charge, $session, $memberId, $amount);
            return;
        }

        // The bill is created.
        $this->billService->createBill($charge, $session, $memberId, $paid, $amount);
    }

    /**
     * @param int $memberId
     * @param bool $paid
     * @param string $amount
     *
     * @return void
     */
    #[Before('checkChargeEdit')]
    public function save(int $memberId, bool $paid, string $amount): void
    {
        $this->saveAmount($memberId, $paid, $amount);
        $this->showTotal();

        $this->refresh($memberId);
    }
}
