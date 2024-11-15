@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $chargeId = Jaxon\jq()->parent()->attr('data-charge-id')->toInt();
  $rqCharge = Jaxon\rq(Ajax\App\Tontine\Options\Charge::class);
  $rqChargePage = Jaxon\rq(Ajax\App\Tontine\Options\ChargePage::class);
@endphp
              <div class="table-responsive" @jxnTarget()>
                <div @jxnOn(['.btn-charge-edit', 'click', ''], $rqCharge->edit($chargeId))></div>
                <div @jxnOn(['.btn-charge-toggle', 'click', ''], $rqCharge->toggle($chargeId))></div>
                <div @jxnOn(['.btn-charge-delete', 'click', ''], $rqCharge->delete($chargeId)
                  ->confirm(__('tontine.charge.questions.delete')))></div>

                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.type') !!}</th>
                      <th>{!! __('common.labels.period') !!}</th>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th class="currency">{!! __('common.labels.amount') !!}</th>
                      <th>{!! __('tontine.charge.labels.lend') !!}</th>
                      <th>{!! __('common.labels.active') !!}</th>
                      <th class="table-menu"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($charges as $charge)
                    <tr>
                      <td>{{ $types[$charge->type] ?? '' }}</td>
                      <td>{{ $periods[$charge->period] ?? '' }}</td>
                      <td>{{ $charge->name }}</td>
                      <td class="currency">{{ $charge->has_amount ?
                        $locale->formatMoney($charge->amount, true) : __('tontine.labels.fees.variable') }}</td>
                      <td class="table-item-toggle">
                        <i class="fa fa-toggle-{{ $charge->lendable ? 'on' : 'off' }}"></i>
                      </td>
                      <td class="table-item-toggle" data-charge-id="{{ $charge->id }}">
                        <a role="link" class="btn-charge-toggle"><i class="fa fa-toggle-{{ $charge->active ? 'on' : 'off' }}"></i></a>
                      </td>
                      <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-charge-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-charge-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav @jxnPagination($rqChargePage)>
                </nav>
              </div>
