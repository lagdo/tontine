@php
  $amountValue = Jaxon\jq("#libre-deposit-input-$receivableId")->val();
  $rqReceivable = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\Receivable::class);
@endphp
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)
                            ->id("libre-deposit-input-$receivableId")->class('form-control')
                            ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqReceivable
                              ->saveAmount($receivableId, $amountValue))><i class="fa fa-save"></i></button>
                          </div>
                        </div>
