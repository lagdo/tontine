@php
  $fundId = jq()->parent()->attr('data-fund-id')->toInt();
  $rqFundFunc = rq(Ajax\App\Guild\Account\FundFunc::class);
  $rqFundPage = rq(Ajax\App\Guild\Account\FundPage::class);
@endphp
                  <div class="table-responsive" id="content-fund-page" @jxnEvent([
                    ['.btn-fund-edit', 'click', $rqFundFunc->edit($fundId)],
                    ['.btn-fund-toggle', 'click', $rqFundFunc->toggle($fundId)],
                    ['.btn-fund-delete', 'click', $rqFundFunc->delete($fundId)
                      ->confirm(__('tontine.fund.questions.delete'))]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th class="table-item-menu">{!! __('common.labels.active') !!}</th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($funds as $fund)
                        <tr>
                          <td>{{ $fund->title }}</td>
                          <td class="table-item-toggle" data-fund-id="{{ $fund->id }}">
                            <a role="link" tabindex="0" class="btn-fund-toggle"><i class="fa fa-toggle-{{ $fund->active ? 'on' : 'off' }}"></i></a>
                          </td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $fund->id,
  'menus' => [[
    'class' => 'btn-fund-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-fund-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqFundPage)>
                    </nav>
                  </div> <!-- End table -->
