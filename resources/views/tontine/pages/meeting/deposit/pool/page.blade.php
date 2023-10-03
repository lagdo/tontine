@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('balance', 'Siak\Tontine\Service\BalanceCalculator')
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
@if ($pool->deposit_fixed)
                      <th class="currency">{!! $locale->formatMoney($balance->getPoolDepositAmount($pool, $session), true) !!}</th>
                      <th class="table-item-menu">{!! __('common.labels.paid') !!}</th>
@else
                      <th class="currency">{!! $locale->formatMoney($balance->getPoolDepositAmount($pool, $session), true) !!}</th>
@endif
                    </tr>
                  </thead>
                  <tbody>
@foreach ($receivables as $receivable)
                    <tr>
                      <td>{{ $receivable->subscription->member->name }}</td>
@if ($pool->deposit_fixed)
                      <td class="currency">{{ $locale->formatMoney($receivable->subscription->pool->amount, true) }}</td>
                      <td class="table-item-menu" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}">
                        {!! paymentLink($receivable->deposit, 'deposit', !$session->opened) !!}
                      </td>
@else
                      <td class="currency" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}" style="width:200px">
@if ($session->closed)
                        @include('tontine.pages.meeting.deposit.libre.closed', [
                          'amount' => !$receivable->deposit ? '' : $locale->formatMoney($receivable->deposit->amount, true),
                        ])
@elseif (!$receivable->deposit)
                        @include('tontine.pages.meeting.deposit.libre.edit', [
                          'id' => $receivable->id,
                          'amount' => '',
                        ])
@else
                        @include('tontine.pages.meeting.deposit.libre.show', [
                          'id' => $receivable->id,
                          'amount' => $locale->formatMoney($receivable->deposit->amount, true),
                        ])
@endif
                      </td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
