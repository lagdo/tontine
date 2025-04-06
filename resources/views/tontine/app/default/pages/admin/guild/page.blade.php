@php
  $guildId = jq()->parent()->attr('data-guild-id')->toInt();
  $rqGuildFunc = rq(Ajax\App\Admin\Guild\GuildFunc::class);
  $rqGuildPage = rq(Ajax\App\Admin\Guild\GuildPage::class);
  $rqMenuFunc = rq(Ajax\App\MenuFunc::class);
@endphp
                <div class="table-responsive" id="content-organisation-page" @jxnTarget()>
                  <div @jxnEvent(['.btn-guild-edit', 'click'], $rqGuildFunc->edit($guildId))></div>
                  <div @jxnEvent(['.btn-guild-choose', 'click'], $rqMenuFunc->saveGuild($guildId))></div>
                  <div @jxnEvent(['.btn-guild-delete', 'click'], $rqGuildFunc->delete($guildId)
                    ->confirm(__('tontine.questions.delete')))></div>

                  <table class="table table-bordered responsive" @jxnTarget()>
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
                        <td>{{ $guild->name }}</td>
                        <td>{{ $guild->city }}</td>
                        <td>{{ $countries[$guild->country_code] }}</td>
                        <td>{{ $currencies[$guild->currency_code] }}</td>
                        <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-guild-id',
  'dataIdValue' => $guild->id,
  'menus' => [[
    'class' => 'btn-guild-choose',
    'text' => __('tontine.actions.choose'),
  ],
  null,[
    'class' => 'btn-guild-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-guild-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                        </td>
                      </tr>
@endforeach
                    </tbody>
                  </table>
                  <nav @jxnPagination($rqGuildPage)>
                  </nav>
                </div>
