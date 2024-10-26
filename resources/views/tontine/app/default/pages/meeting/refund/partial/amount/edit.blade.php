@php
  $amountValue = Jaxon\jq('input', Jaxon\jq()->parent()->parent())->val()->toInt();
  $rqPartialRefund = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\PartialRefund::class);
@endphp
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                             ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append" data-debt-id="{{ $debt->id }}">
                            <button type="button" class="btn btn-primary" @jxnClick($rqPartialRefund
                              ->saveAmount($debt->id, $amountValue))><i class="fa fa-save"></i></button>
                          </div>
                        </div>
