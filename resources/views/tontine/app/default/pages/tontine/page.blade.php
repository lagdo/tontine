@php
  $tontineId = jq()->parent()->attr('data-tontine-id')->toInt();
  $rqOrganisationFunc = rq(Ajax\App\Admin\Organisation\OrganisationFunc::class);
  $rqOrganisationPage = rq(Ajax\App\Admin\Organisation\OrganisationPage::class);
  $rqMenuFunc = rq(Ajax\App\MenuFunc::class);
@endphp
                <div class="table-responsive" id="content-organisation-page" @jxnTarget()>
                  <div @jxnEvent(['.btn-tontine-edit', 'click'], $rqOrganisationFunc->edit($tontineId))></div>
                  <div @jxnEvent(['.btn-tontine-choose', 'click'], $rqMenuFunc->saveOrganisation($tontineId))></div>
                  <div @jxnEvent(['.btn-tontine-delete', 'click'], $rqOrganisationFunc->delete($tontineId)
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
@foreach ($tontines as $tontine)
                      <tr>
                        <td>{{ $tontine->name }}</td>
                        <td>{{ $tontine->city }}</td>
                        <td>{{ $countries[$tontine->country_code] }}</td>
                        <td>{{ $currencies[$tontine->currency_code] }}</td>
                        <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
'dataIdKey' => 'data-tontine-id',
'dataIdValue' => $tontine->id,
'menus' => [[
  'class' => 'btn-tontine-edit',
  'text' => __('common.actions.edit'),
],[
  'class' => 'btn-tontine-choose',
  'text' => __('tontine.actions.choose'),
],[
  'class' => 'btn-tontine-delete',
  'text' => __('common.actions.delete'),
]],
])
                        </td>
                      </tr>
@endforeach
                    </tbody>
                  </table>
                  <nav @jxnPagination($rqOrganisationPage)>
                  </nav>
                </div>
