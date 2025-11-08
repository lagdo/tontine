@php
  $auctionId = jq()->parent()->attr('data-auction-id')->toInt();
  $rqAuctionFunc = rq(Ajax\App\Meeting\Session\Pool\Remitment\AuctionFunc::class);
  $rqAuctionPage = rq(Ajax\App\Meeting\Session\Pool\Remitment\AuctionPage::class);
@endphp
                  <div class="table-responsive" id="content-session-auctions-page" @jxnEvent([
                    ['.btn-toggle-payment', 'click', $rqAuctionFunc->togglePayment($auctionId)]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($auctions as $auction)
                        <tr>
                          <td>
                            <div>{{ $auction->member->name }}</div>
@if ($auction->paid)
                            <div>{{ $auction->session->title }}</div>
@endif
                          </td>
                          <td class="currency">
                            <div>{{ $locale->formatMoney($auction->amount) }}</div>
                            <div>{{ __('meeting.remitment.labels.auction') }}</div>
                          </td>
                          <td class="table-item-menu" data-auction-id="{{ $auction->id }}">
@if ($session->opened)
                            <a role="link" tabindex="0" class="btn-toggle-payment"><i class="fa fa-toggle-{{ $auction->paid ? 'on' : 'off' }}"></i></a>
@else
                            <i class="fa fa-toggle-{{ $auction->paid ? 'on' : 'off' }}"></i>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqAuctionPage)>
                    </nav>
                  </div> <!-- End table -->
