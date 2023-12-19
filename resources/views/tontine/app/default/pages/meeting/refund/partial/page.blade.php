@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('meeting.labels.member') !!}</th>
                      <th class="currency">{!! __('common.labels.amount') !!}</th>
                      <th class="table-item-menu">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach($refunds as $refund)
                    <tr>
                      <td>
                        {{ $refund->debt->loan->member->name }}<br/>{{ $refund->debt->loan->session->title }}
                      </td>
                      <td class="currency">
                        {{ $locale->formatMoney($refund->amount, true) }}<br/>
                        {{ $locale->formatMoney($debtCalculator->getDebtAmount($session, $refund->debt), true)
                          }} - {{ __('meeting.loan.labels.' . $refund->debt->type) }}
                      </td>
                      <td class="table-item-menu">
@if (!$refund->debt->refund)
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-refund-id',
  'dataIdValue' => $refund->id,
  'menus' => [[
    'class' => 'btn-del-partial-refund',
    'text' => __('common.actions.delete'),
  ]],
])
@else
                        <i class="fa fa-toggle-on"></i>
@endif
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
