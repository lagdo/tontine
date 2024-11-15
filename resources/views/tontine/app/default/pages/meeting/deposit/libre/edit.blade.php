@php
  $amount = Jaxon\jq('input', jq()->parent()->parent())->val();
  $rqPool = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\Pool::class);
@endphp
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                            ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqPool
                              ->saveAmount($receivableId, $amount))><i class="fa fa-save"></i></button>
                          </div>
                        </div>
