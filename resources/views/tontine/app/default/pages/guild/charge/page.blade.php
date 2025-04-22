@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqChargeFunc = rq(Ajax\App\Guild\Charge\ChargeFunc::class);
  $rqChargePage = rq(Ajax\App\Guild\Charge\ChargePage::class);
@endphp
              <div class="table-responsive" id="content-charge-page" @jxnTarget()>
                <div @jxnEvent(['.btn-charge-edit', 'click'], $rqChargeFunc->edit($chargeId))></div>
                <div @jxnEvent(['.btn-charge-toggle', 'click'], $rqChargeFunc->toggle($chargeId))></div>
                <div @jxnEvent(['.btn-charge-delete', 'click'], $rqChargeFunc->delete($chargeId)
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
                        $locale->formatMoney($charge->amount) : __('tontine.labels.fees.variable') }}</td>
                      <td class="table-item-toggle">
                        <i class="fa fa-toggle-{{ $charge->lendable ? 'on' : 'off' }}"></i>
                      </td>
                      <td class="table-item-toggle" data-charge-id="{{ $charge->id }}">
                        <a role="link" tabindex="0" class="btn-charge-toggle"><i class="fa fa-toggle-{{ $charge->active ? 'on' : 'off' }}"></i></a>
                      </td>
                      <td class="table-item-menu">
@include('tontine::parts.table.menu', [
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
