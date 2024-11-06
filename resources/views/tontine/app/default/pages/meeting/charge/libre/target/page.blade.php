@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqTargetPage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\TargetPage::class);
@endphp
                  <div class="table-responsive" id="meeting-fee-libre-target">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.name') }}</th>
                          <th class="currency">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
@php
  $paid = $member->paid ?? 0;
  $remaining = $target->amount > $paid ? $target->amount - $paid : 0;
@endphp
                        <tr>
                          <td>{{ $member->name }}@if ($remaining > 0)<br/>{{ __('meeting.target.labels.remaining',
                            ['amount' => $locale->formatMoney($remaining, true)]) }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($paid, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqTargetPage)>
                    </nav>
                  </div> <!-- End table -->
