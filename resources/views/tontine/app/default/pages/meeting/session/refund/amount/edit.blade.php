@php
  $amountValue = pm()->input("refund-amount-edit-{$debt->id}");
  $rqAmountFunc = rq(Ajax\App\Meeting\Session\Credit\Refund\AmountFunc::class);
@endphp
                        <div class="input-group">
                          {!! $html->text('amount', $amount)->class('form-control')
                            ->id("refund-amount-edit-{$debt->id}")
                            ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqAmountFunc
                              ->save($debt->id, $amountValue))><i class="fa fa-save"></i></button>
                          </div>
                        </div>
