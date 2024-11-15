@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $auctionId = Jaxon\jq()->parent()->attr('data-auction-id')->toInt();
  $rqAuction = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Auction::class);
  $rqAuctionPage = Jaxon\rq(Ajax\App\Meeting\Session\Pool\AuctionPage::class);
@endphp
                  <div class="table-responsive" id="meeting-auctions-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-toggle-payment', 'click'], $rqAuction->togglePayment($auctionId))></div>

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
                            {{ $auction->member->name }}<br/>
                            {{ $auction->remitment->payable->session->title }}@if ($auction->paid) - {{ $auction->session->title }}@endif
                          </td>
                          <td class="currency">
                            {{ $locale->formatMoney($auction->amount) }}<br/>{{ __('meeting.remitment.labels.auction') }}
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
