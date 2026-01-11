@php
  $guildId = jq()->parent()->attr('data-guild-id')->toInt();
  $rqMenuFunc = rq(Ajax\Page\Header\MenuFunc::class);
  $rqGuildFunc = rq(Ajax\App\Admin\Guild\GuildFunc::class);
  $rqGuildPage = rq(Ajax\App\Admin\Guild\GuildPage::class);
@endphp
                <div class="table-responsive" id="content-organisation-page" @jxnEvent([
                  ['.btn-guild-edit', 'click', $rqGuildFunc->edit($guildId)],
                  ['.btn-guild-choose', 'click', $rqMenuFunc->saveGuild($guildId)],
                  ['.btn-guild-delete', 'click', $rqGuildFunc->delete($guildId)
                    ->confirm(__('tontine.questions.delete'))]])>

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
                        <td>{{ $guild->name }}</td>
                        <td>{{ $guild->city }}</td>
                        <td>{{ $countries[$guild->country_code] }}</td>
                        <td>{{ $currencies[$guild->currency_code] }}</td>
                        <td class="table-item-menu">
@include('tontine_app::parts.table.menu', [
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
