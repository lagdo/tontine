@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $savingId = Jaxon\jq()->parent()->attr('data-saving-id')->toInt();
  $rqSaving = Jaxon\rq(Ajax\App\Meeting\Session\Saving\Saving::class);
  $rqSavingPage = Jaxon\rq(Ajax\App\Meeting\Session\Saving\SavingPage::class);
@endphp
                  <div class="table-responsive" id="meeting-savings-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-saving-edit', 'click'], $rqSaving->editSaving($savingId))></div>
                    <div @jxnEvent(['.btn-saving-delete', 'click'], $rqSaving->deleteSaving($savingId)
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
                          <td class="currency">{{ $locale->formatMoney($saving->amount, true) }}</td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
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
