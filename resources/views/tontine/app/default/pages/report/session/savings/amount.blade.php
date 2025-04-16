@php
  $amountValue = pm()->input('fund-profit-amount')->toInt();
  $rqProfit = rq(Ajax\App\Report\Session\Saving\Profit::class);
@endphp
                      <div class="input-group">
                        {!! $html->text('fund-profit-amount', $profitAmount)->class('form-control')
                          ->id('fund-profit-amount')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button @jxnClick($rqProfit->amount($amountValue)) type="button" class="btn btn-primary"><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
