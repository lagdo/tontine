@php
  $roundId = jq()->parent()->attr('data-round-id')->toInt();
  $rqSelect = rq(Ajax\App\Tontine\Select::class);
  $rqSession = rq(Ajax\App\Planning\Session\Session::class);
  $rqRound = rq(Ajax\App\Planning\Session\Round::class);
  $rqRoundPage = rq(Ajax\App\Planning\Session\RoundPage::class);
@endphp
                  <div class="table-responsive" @jxnTarget()>
                    <div @jxnEvent(['.btn-round-edit', 'click'], $rqRound->edit($roundId))></div>
                    <div @jxnEvent(['.btn-round-sessions', 'click'], $rqSession->round($roundId))></div>
                    <div @jxnEvent(['.btn-round-select', 'click'], $rqSelect->saveRound($roundId))></div>
                    <div @jxnEvent(['.btn-round-delete', 'click'], $rqRound->delete($roundId)
                      ->confirm(__('tontine.round.questions.delete')))></div>

                    <table class="table table-bordered responsive">
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
    'class' => 'btn-round-sessions',
    'text' => __('tontine.pool.actions.sessions'),
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
                    <nav @jxnPagination($rqRoundPage)>
                    </nav>
                  </div> <!-- End table -->
