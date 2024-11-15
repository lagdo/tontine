@php
  $amountValue = Jaxon\jq('input', Jaxon\jq()->parent()->parent())->val();
  $rqMember = Jaxon\rq(Ajax\App\Meeting\Session\Charge\Libre\Member::class);
@endphp
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                             ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append" data-member-id="{{ $id }}">
                            <button type="button" class="btn btn-primary" @jxnClick($rqMember
                              ->saveBill($memberId, $amountValue))><i class="fa fa-save"></i></button>
                          </div>
                        </div>
