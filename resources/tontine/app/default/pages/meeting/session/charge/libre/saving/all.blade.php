@if ($billCount > 0)
@php
  $fundId = Jaxon\select('settlement-saving-fund')->toInt();
  $rqSavingFunc = rq(Ajax\App\Meeting\Session\Charge\Libre\SavingFunc::class);
  $menus = [];
  if($settlementCount < $billCount)
  {
    $menus[] = [
      'class' => 'btn-settlement-all-create',
      'text' => __('common.actions.add'),
    ];
  }
  if($settlementCount > 0)
  {
    $menus[] = [
      'class' => 'btn-settlement-all-delete',
      'text' => __('common.actions.delete'),
    ];
  }
@endphp
                <div class="btn-group ml-2" role="group" @jxnEvent([
                  ['.btn-settlement-all-create', 'click', $rqSavingFunc->addSettlements($fundId)
                    ->confirm(__('meeting.settlement.questions.create-all', [
                      'count' => $billCount - $settlementCount,
                    ]))],
                  ['.btn-settlement-all-delete', 'click', $rqSavingFunc->delSettlements()
                    ->confirm(__('meeting.settlement.questions.delete-all', [
                      'count' => $settlementCount,
                    ]))],
                ])>
@include('tontine_app::parts.table.menu', [
  'btnSize' => '',
  'btnText' => __('common.labels.all'),
  'menus' => $menus,
])
                </div>
@endif
