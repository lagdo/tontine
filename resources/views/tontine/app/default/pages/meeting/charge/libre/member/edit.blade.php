@php
  $amountValue = Jaxon\jq("#member-charge-input-$id")->val();
  $rqMember = Jaxon\rq(Ajax\App\Meeting\Session\Charge\Libre\Member::class);
  $handler = $hasBill ? $rqMember->saveBill($id, $amountValue) :
    $rqMember->addBill($id, Jaxon\pm()->checked('check-fee-libre-paid'), $amountValue);
@endphp
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)
                            ->id("member-charge-input-$id")->class('form-control')
                            ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append" data-member-id="{{ $id }}">
                            <button type="button" class="btn btn-primary" @jxnClick($handler)><i class="fa fa-save"></i></button>
                          </div>
                        </div>
