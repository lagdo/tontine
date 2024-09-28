@php
  $inputAmount = Jaxon\pm()->input('fund-profit-amount')->toInt();
  $rqFund = Jaxon\rq(App\Ajax\Web\Report\Session\Saving\Fund::class);
@endphp
                    <div class="col">&nbsp;</div>
                    <div class="col-auto">
                      <div class="input-group">
                        {!! $htmlBuilder->text('fund-profit-amount', $profitAmount)->class('form-control')
                          ->id('fund-profit-amount')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqFund->amount($inputAmount))><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
                    </div>
