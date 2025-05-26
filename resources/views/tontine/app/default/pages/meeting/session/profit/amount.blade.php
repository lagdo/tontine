@php
  $amountValue = pm()->input('fund-profit-amount')->toInt();
@endphp
                      <div class="input-group">
                        {!! $html->text('fund-profit-amount', $locale->getMoneyValue($profitAmount))
                          ->class('form-control')->id('fund-profit-amount')
                          ->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button @jxnClick($rqProfit->amount($amountValue)) type="button" class="btn btn-primary"><i class="fa fa-sync"></i></button>
@if($withSave ?? false)
@php
  $rqAmountFunc = rq(Ajax\App\Meeting\Session\Saving\AmountFunc::class);
@endphp
                          <button @jxnClick($rqAmountFunc->saveProfitAmount($amountValue)) type="button" class="btn btn-primary"><i class="fa fa-save"></i></button>
@endif
                        </div>
                      </div>
