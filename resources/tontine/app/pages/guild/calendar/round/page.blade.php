@php
  $roundId = jq()->parent()->attr('data-round-id')->toInt();
  $rqMenuFunc = rq(Ajax\Page\Header\RoundMenuFunc::class);
  $rqRoundFunc = rq(Ajax\App\Guild\Calendar\RoundFunc::class);
  $rqRoundPage = rq(Ajax\App\Guild\Calendar\RoundPage::class);
  $rqSession = rq(Ajax\App\Guild\Calendar\Session::class);
@endphp
                  <div class="table-responsive" id="content-planning-rounds-page" @jxnEvent([
                    ['.btn-round-edit', 'click', $rqRoundFunc->edit($roundId)],
                    ['.btn-round-sessions', 'click', $rqSession->round($roundId)],
                    ['.btn-round-select', 'click', $rqMenuFunc->selectRound($roundId)],
                    ['.btn-round-delete', 'click', $rqRoundFunc->delete($roundId)
                      ->confirm(__('tontine.round.questions.delete'))]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>{!! __('common.labels.start') !!}</th>
                          <th>{!! __('common.labels.end') !!}</th>
                          <th>&nbsp;</th>
                          <th class="table-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($rounds as $round)
                        <tr>
                          <td>{{ $round->title }}</td>
                          <td>{{ !$round->start_date ? '' : $round->date('start_date') }}</td>
                          <td>{{ !$round->end_date ? '' : $round->date('end_date') }}</td>
                          <td>{{ $round->notes ?? '' }}</td>
                          <td class="table-item-menu">
@include('tontine_app::parts.table.menu', [
  'dataIdKey' => 'data-round-id',
  'dataIdValue' => $round->id,
  'menus' => [[
    'class' => 'btn-round-select',
    'text' => __('tontine.actions.choose'),
  ],
  null,[
    'class' => 'btn-round-sessions',
    'text' => __('tontine.actions.sessions'),
  ],
  null,[
    'class' => 'btn-round-edit',
    'text' => __('common.actions.edit'),
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
