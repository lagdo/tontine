@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $savingId = jq()->parent()->attr('data-saving-id')->toInt();
  $rqSavingFunc = rq(Ajax\App\Meeting\Session\Saving\SavingFunc::class);
  $rqSavingPage = rq(Ajax\App\Meeting\Session\Saving\SavingPage::class);
@endphp
                  <div class="table-responsive" id="content-session-savings-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-saving-edit', 'click'], $rqSavingFunc->edit($savingId))></div>
                    <div @jxnEvent(['.btn-saving-delete', 'click'], $rqSavingFunc->delete($savingId)
                      ->confirm(__('meeting.saving.questions.delete')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($savings as $saving)
                        <tr>
                          <td>{{ $saving->member }}<br/>{!! $saving->fund ?
                            $saving->fund->title : __('tontine.fund.labels.default') !!}</td>
                          <td class="currency">{{ $locale->formatMoney($saving->amount) }}</td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-saving-id',
  'dataIdValue' => $saving->id,
  'menus' => [[
    'class' => 'btn-saving-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-saving-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSavingPage)>
                    </nav>
                  </div> <!-- End table -->
