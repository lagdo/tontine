@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{{ $savingCount }}<br/>{{ $locale->formatMoney($savingSum, true) }}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($savings as $saving)
                        <tr>
                          <td>{{ $saving->member }}<br/>{!! $saving->fund ?
                            $saving->fund->title : __('tontine.fund.labels.default') !!}</td>
                          <td class="currency">{{ $locale->formatMoney($saving->amount, true) }}</td>
                          <td class="table-item-menu">
@if($session->opened)
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-saving-id',
  'dataIdValue' => $saving->id,
  'menus' => [[
    'class' => 'btn-saving-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-saving-delete',
    'text' => __('common.actions.delete'),
  ]],
])
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
{!! $pagination !!}
