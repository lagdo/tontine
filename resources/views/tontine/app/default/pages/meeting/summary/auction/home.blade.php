@php
  $rqAuction = rq(Ajax\App\Meeting\Summary\Pool\Remitment\Auction::class);
  $rqAuctionPage = rq(Ajax\App\Meeting\Summary\Pool\Remitment\AuctionPage::class);
  $rqRemitment = rq(Ajax\App\Meeting\Summary\Pool\Remitment\Remitment::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.auctions') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqRemitment->render())>{{ __('meeting.titles.remitments') }}</button>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqAuction->render())><i class="fa fa-sync"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqAuction->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqAuctionPage)>
                  </div>
