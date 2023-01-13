<?php

namespace App\Ajax\App;

use Siak\Tontine\Service\Charge\ChargeService;
use Siak\Tontine\Service\FundService;
use Siak\Tontine\Service\MemberService;
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
     * @var FundService
     */
    public FundService $fundService;

    /**
     * @databag faker
     * @di $memberService
     */
    public function members()
    {
        $count = intval($this->bag('faker')->get('member.count'));
        $members = $this->memberService->getFakeMembers($count);
        for($i = 0; $i < $count; $i++)
        {
            $this->jq("#member_gender_$i")->val($members[$i]->gender);
            $this->jq("#member_name_$i")->val($members[$i]->name);
            $this->jq("#member_email_$i")->val($members[$i]->email);
            $this->jq("#member_phone_$i")->val($members[$i]->phone);
        }

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
     * @di $fundService
     */
    public function funds()
    {
        $count = intval($this->bag('faker')->get('fund.count'));
        $funds = $this->fundService->getFakeFunds($count);
        for($i = 0; $i < $count; $i++)
        {
            $this->jq("#fund_title_$i")->val($funds[$i]->title);
            $this->jq("#fund_amount_$i")->val($funds[$i]->amount);
            $this->jq("#fund_notes_$i")->val($funds[$i]->notes);
        }

        return $this->response;
    }
}
