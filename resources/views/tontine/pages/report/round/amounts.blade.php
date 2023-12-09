@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $cash = 0;
@endphp
              <div class="section-title mt-0">{!! __('meeting.titles.amounts') !!}</div>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th></th>
                      <th>{!! __('meeting.titles.auctions') !!}</th>
                      <th>{!! __('meeting.titles.fees') !!}</th>
                      <th>{!! __('meeting.titles.loans') !!}</th>
                      <th>{!! __('meeting.titles.refunds') !!}</th>
                      <th>{!! __('meeting.titles.savings') !!}</th>
                      <th>{!! __('meeting.titles.disbursements') !!}</th>
                      <th>{!! __('figures.titles.end') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <th>{{ $session->title }}</th>
@if($session->pending)
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
@else
@php
  $settlement = $settlements[$session->id] ?? 0;
  $auction = $auctions[$session->id] ?? 0;
  $saving = $savings[$session->id] ?? 0;
  $loan = $loans[$session->id] ?? 0;
  $refund = $refunds[$session->id] ?? 0;
  $disbursement = $disbursements[$session->id] ?? 0;
  $balance = $auction + $settlement + $refund + $saving - $loan - $disbursement;
  $cash += $balance;
@endphp
                      <td class="currency"><b>{!! $locale->formatMoney($auction, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($settlement, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($loan, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($refund, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($saving, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($disbursement, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($balance, false) !!}<br/>{!!
                        $locale->formatMoney($cash, false) !!}</b></td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
