<?php

namespace App\Ajax\Web;

use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Tontine\ChargeService;
use Siak\Tontine\Service\Tontine\MemberService;
use App\Ajax\CallableClass;

use function intval;

class Faker extends CallableClass
{
    /**
     * @var MemberService
     */
    public MemberService $memberService;

    /**
     * @var ChargeService
     */
    public ChargeService $chargeService;

    /**
     * @var PoolService
     */
    public PoolService $poolService;

    /**
     * @databag faker
     * @di $memberService
     */
    public function members()
    {
        $count = intval($this->bag('faker')->get('member.count', 10));
        $members = $this->memberService->getFakeMembers($count);
        $html = $members->map(function($member) {
            return $member->name . ';' . $member->email;
        })->join("\n");
        $this->response->html('new-members-list', $html);

        return $this->response;
    }

    /**
     * @databag faker
     * @di $chargeService
     */
    public function charges()
    {
        $count = intval($this->bag('faker')->get('charge.count'));
        $charges = $this->chargeService->getFakeCharges($count);
        for($i = 0; $i < $count; $i++)
        {
            $this->jq("#charge_type_$i")->val($charges[$i]->type);
            $this->jq("#charge_period_$i")->val($charges[$i]->period);
            $this->jq("#charge_name_$i")->val($charges[$i]->name);
            $this->jq("#charge_amount_$i")->val($charges[$i]->amount);
        }

        return $this->response;
    }

    /**
     * @databag faker
     * @di $poolService
     */
    public function pools()
    {
        $count = intval($this->bag('faker')->get('pool.count'));
        $pools = $this->poolService->getFakePools($count);
        for($i = 0; $i < $count; $i++)
        {
            $this->jq("#pool_title_$i")->val($pools[$i]->title);
            $this->jq("#pool_amount_$i")->val($pools[$i]->amount);
            $this->jq("#pool_notes_$i")->val($pools[$i]->notes);
        }

        return $this->response;
    }
}
