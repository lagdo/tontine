@php
  $guildId = jq()->parent()->attr('data-guild-id')->toInt();
  $rqMenuFunc = rq(Ajax\Page\MenuFunc::class);
@endphp
                  <div class="table-responsive" @jxnEvent([
                    ['.btn-guest-guild-choose', 'click', $rqMenuFunc->saveGuild($guildId)]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th>{!! __('common.labels.city') !!}</th>
                          <th>{!! __('common.labels.country') !!}</th>
                          <th>{!! __('common.labels.currency') !!}</th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($guilds as $guild)
                        <tr>
                          <td>{{ $guild->name }}<br/><b>{{ $guild->user->name }}</b></td>
                          <td>{{ $guild->city }}</td>
                          <td>{{ $countries[$guild->country_code] }}</td>
                          <td>{{ $currencies[$guild->currency_code] }}</td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-guild-id',
  'dataIdValue' => $guild->id,
  'menus' => [[
    'class' => 'btn-guest-guild-choose',
    'text' => __('tontine.actions.choose'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div>
