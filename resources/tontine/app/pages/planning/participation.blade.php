@php
  $rqMember = rq(Ajax\App\Planning\Member\Member::class);
  $rqCharge = rq(Ajax\App\Planning\Charge\Charge::class);
@endphp
          <div class="row sm-screen-selector mt-2 mb-1" id="finance-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="content-members-home" type="button" class="btn btn-primary">
                  {{ __('tontine.member.titles.members') }}
                </button>
                <button data-target="content-charges-home" type="button" class="btn btn-outline-primary">
                  {{ __('tontine.charge.titles.charges') }}
                </button>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-members-home" @jxnBind($rqMember)>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="content-charges-home" @jxnBind($rqCharge)>
            </div>
          </div>
