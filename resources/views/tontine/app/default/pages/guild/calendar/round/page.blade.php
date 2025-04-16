@php
  $roundId = jq()->parent()->attr('data-round-id')->toInt();
  $rqRoundFunc = rq(Ajax\App\Guild\Calendar\RoundFunc::class);
  $rqRoundPage = rq(Ajax\App\Guild\Calendar\RoundPage::class);
  $rqMenuFunc = rq(Ajax\App\MenuFunc::class);
  $rqSession = rq(Ajax\App\Guild\Calendar\Session::class);
@endphp
                  <div class="table-responsive" id="content-planning-rounds-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-round-edit', 'click'], $rqRoundFunc->edit($roundId))></div>
                    <div @jxnEvent(['.btn-round-sessions', 'click'], $rqSession->round($roundId))></div>
                    <div @jxnEvent(['.btn-round-select', 'click'], $rqMenuFunc->saveRound($roundId))></div>
                    <div @jxnEvent(['.btn-round-delete', 'click'], $rqRoundFunc->delete($roundId)
                      ->confirm(__('tontine.round.questions.delete')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>{!! __('common.labels.dates') !!}</th>
                          <th>{!! __('common.labels.options') !!}</th>
                          <th class="table-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($rounds as $round)
                        <tr>
                          <td>{{ $round->title }}<br/>{{ $round->notes ?? '' }}</td>
                          <td>
                            {{ !$round->start_at ? '' : $round->start_at->translatedFormat(__('tontine.date.format')) }}<br/>
                            {{ !$round->end_at ? '' : $round->end_at->translatedFormat(__('tontine.date.format')) }}
                          </td>
                          <td>
                            <i class="fa fa-toggle-{{ $round->add_default_fund ? 'on' : 'off' }}"></i>
                            {{ __('tontine.round.labels.savings') }}
                          </td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
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
