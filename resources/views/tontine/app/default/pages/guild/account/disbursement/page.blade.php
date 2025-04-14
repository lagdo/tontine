@php
  $accountId = jq()->parent()->attr('data-account-id')->toInt();
  $rqDisbursementFunc = rq(Ajax\App\Guild\Account\DisbursementFunc::class);
  $rqDisbursementPage = rq(Ajax\App\Guild\Account\DisbursementPage::class);
@endphp
                  <div class="table-responsive" id="content-account-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-account-edit', 'click'], $rqDisbursementFunc->edit($accountId))></div>
                    <div @jxnEvent(['.btn-account-toggle', 'click'], $rqDisbursementFunc->toggle($accountId))></div>
                    <div @jxnEvent(['.btn-account-delete', 'click'], $rqDisbursementFunc->delete($accountId)
                      ->confirm(__('tontine.account.questions.delete')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="table-item-menu">{!! __('common.labels.active') !!}</th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($accounts as $account)
                        <tr>
                          <td>{{ $account->name }}</td>
                          <td class="table-item-toggle" data-account-id="{{ $account->id }}">
                            <a role="link" tabindex="0" class="btn-account-toggle"><i class="fa fa-toggle-{{ $account->active ? 'on' : 'off' }}"></i></a>
                          </td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-account-id',
  'dataIdValue' => $account->id,
  'menus' => [[
    'class' => 'btn-account-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-account-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqDisbursementPage)>
                    </nav>
                  </div> <!-- End table -->
