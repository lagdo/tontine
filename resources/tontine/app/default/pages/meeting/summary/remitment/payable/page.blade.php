                  <div class="table-responsive" id="content-session-pool-remitments">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($payables as $payable)
                        <tr>
                          <td>{{ $payable->member }}</td>
                          <td class="currency">
                            <div>{{ $locale->formatMoney($payable->amount) }}</div>
@if ($payable->remitment && $payable->remitment->auction)
                            <div>{{ __('meeting.remitment.labels.auction') }}: {{
                              $locale->formatMoney($payable->remitment->auction->amount) }}</div>
@endif
                          </td>
                          <td class="table-item-menu">
                            <i class="fa @if($payable->remitment) fa-toggle-on @else fa-toggle-off @endif"></i>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
