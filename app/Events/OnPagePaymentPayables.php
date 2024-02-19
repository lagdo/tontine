<?php
 
namespace App\Events;
 
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Session;

class OnPagePaymentPayables
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Member $member
     * @param Session $session
     * @param Collection $receivables
     * @param Collection $bills
     * @param Collection $debts
     */
    public function __construct(public Member $member, public Session $session,
        public Collection $receivables, public Collection $bills, public Collection $debts)
    {}
}
