@php
  $amountValue = pm()->input('fund-profit-amount')->toInt();
  $rqFund = rq(Ajax\App\Report\Session\Saving\Fund::class);
@endphp
                    <div class="col">&nbsp;</div>
                    <div class="col-auto">
                      <div class="input-group">
                        {!! $htmlBuilder->text('fund-profit-amount', $profitAmount)->class('form-control')
                          ->id('fund-profit-amount')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqFund
                            ->amount($amountValue))><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
                    </div>
