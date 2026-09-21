@php
  $rqTargetPage = rq(Ajax\App\Meeting\Session\Charge\Libre\TargetPage::class);
@endphp
                  <div class="table-responsive" id="content-session-fee-libre-target">
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
                          <td>
                            <div>{{ $member->name }}</div>
@if ($remaining > 0)
                            <div>{{ __('meeting.target.labels.remaining',
                              ['amount' => $locale->formatMoney($remaining)]) }}</div>
@endif
                          </td>
                          <td class="currency">{{ $locale->formatMoney($paid) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqTargetPage)>
                    </nav>
                  </div> <!-- End table -->
