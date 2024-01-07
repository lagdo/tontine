@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('balance', 'Siak\Tontine\Service\BalanceCalculator')
@php
  $depositAmount = $balance->getPoolDepositAmount($pool, $session);
@endphp
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
@if ($pool->deposit_fixed)
                      <th class="currency">{!! $depositAmount > 0 ? $locale->formatMoney($depositAmount, true) : '&nbsp;' !!}</th>
                      <th class="table-item-menu">
@if ($depositCount < $receivableCount)
                        <a href="javascript:void(0)" class="btn-add-all-deposits"><i class="fa fa-toggle-off"></i></a>
@else
                        <a href="javascript:void(0)" class="btn-del-all-deposits"><i class="fa fa-toggle-on"></i></a>
@endif
                      </th>
@else
                      <th class="currency">{!! $depositAmount > 0 ? $locale->formatMoney($depositAmount, true) : '&nbsp;' !!}</th>
@endif
                    </tr>
                  </thead>
                  <tbody>
@foreach ($receivables as $receivable)
                    <tr>
                      <td>{{ $receivable->member }}</td>
@if ($pool->deposit_fixed)
                      <td class="currency">{{ $locale->formatMoney($receivable->amount, true) }}</td>
                      <td class="table-item-menu" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}">
                        {!! paymentLink($receivable->deposit, 'deposit', !$session->opened) !!}
                      </td>
@else
                      <td class="currency" id="receivable-{{ $receivable->id }}" data-receivable-id="{{ $receivable->id }}" style="width:200px">
@if ($session->closed)
                        @include('tontine.app.default.pages.meeting.deposit.libre.closed', [
                          'amount' => !$receivable->deposit ? '' : $locale->formatMoney($receivable->deposit->amount, true),
                        ])
@elseif (!$receivable->deposit)
                        @include('tontine.app.default.pages.meeting.deposit.libre.edit', [
                          'id' => $receivable->id,
                          'amount' => '',
                        ])
@else
                        @include('tontine.app.default.pages.meeting.deposit.libre.show', [
                          'id' => $receivable->id,
                          'amount' => $locale->formatMoney($receivable->deposit->amount, false),
                          'editable' => $receivable->deposit->editable,
                        ])
@endif
                      </td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
