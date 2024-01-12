                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>{!! __('common.labels.title') !!}</th>
                              <th>{!! __('common.labels.dates') !!}</th>
                              <th class="table-menu"></th>
                            </tr>
                          </thead>
                          <tbody>
@foreach ($rounds as $round)
                            <tr>
                              <td>{{ $round->title }}</td>
                              <td>
                                {{ !$round->start_at ? '' : $round->start_at->translatedFormat(__('tontine.date.format')) }}<br/>
                                {{ !$round->end_at ? '' : $round->end_at->translatedFormat(__('tontine.date.format')) }}
                              </td>
                              <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-round-id',
  'dataIdValue' => $round->id,
  'menus' => [[
    'class' => 'btn-round-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-round-select',
    'text' => __('tontine.actions.choose'),
  ],[
    'class' => 'btn-round-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                              </td>
                            </tr>
@endforeach
                          </tbody>
                        </table>
{!! $pagination !!}
