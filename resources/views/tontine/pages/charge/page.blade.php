              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.type') !!}</th>
                      <th>{!! __('common.labels.period') !!}</th>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th class="currency">{!! __('common.labels.amount') !!}</th>
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
                      <td class="currency">{{ $charge->has_amount ? $charge->money('amount') : __('tontine.labels.fees.variable') }}</td>
                      <td class="table-item-menu" data-charge-id="{{ $charge->id }}">
                        <a href="javascript:void(0)" class="btn-charge-toggle"><i class="fa fa-toggle-{{ $charge->active ? 'on' : 'off' }}"></i></a>
                      </td>
                      <td class="table-item-menu">
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-charge-edit',
    'text' => __('common.actions.edit'),
  ]],
])
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
{!! $pagination !!}
              </div>
