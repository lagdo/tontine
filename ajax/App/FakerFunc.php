<?php

namespace Ajax\App;

use Siak\Tontine\Service\Guild\ChargeService;
use Siak\Tontine\Service\Guild\MemberService;
use Siak\Tontine\Service\Planning\PoolService;
use Ajax\FuncComponent;

use function intval;

class FakerFunc extends FuncComponent
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
            $this->response->jq("#charge_type_$i")->val($charges[$i]->type);
            $this->response->jq("#charge_period_$i")->val($charges[$i]->period);
            $this->response->jq("#charge_name_$i")->val($charges[$i]->name);
            $this->response->jq("#charge_amount_$i")->val($charges[$i]->amount);
        }
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
            $this->response->jq("#pool_title_$i")->val($pools[$i]->title);
            $this->response->jq("#pool_amount_$i")->val($pools[$i]->amount);
            $this->response->jq("#pool_notes_$i")->val($pools[$i]->notes);
        }
    }
}
