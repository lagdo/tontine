                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th class="currency">{!! __('common.labels.paid') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($receivables as $receivable)
                    <tr>
                      <td>{{ $receivable->subscription->member->name }}</td>
                      <td class="currency" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}" style="width:200px">
@if ($tontine->is_libre)
@if ($session->closed)
                        @include('tontine.pages.meeting.deposit.libre.closed', [
                          'amount' => !$receivable->deposit ? '' : $receivable->deposit->amount,
                        ])
@elseif (!$receivable->deposit)
                        @include('tontine.pages.meeting.deposit.libre.edit', [
                          'id' => $receivable->id,
                          'amount' => '',
                        ])
@else
                        @include('tontine.pages.meeting.deposit.libre.show', [
                          'id' => $receivable->id,
                          'amount' => $receivable->deposit->amount,
                        ])
@endif
@else
                        {!! paymentLink($receivable->deposit, 'deposit', $session->closed) !!}
@endif
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
