@if ($billCount > 0)
@php
  $rqSettlementFunc = rq(Ajax\App\Meeting\Session\Charge\Fixed\SettlementFunc::class);
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
                  ['.btn-settlement-all-create', 'click', $rqSettlementFunc->addSettlements()
                    ->confirm(__('meeting.settlement.questions.create-all', [
                      'count' => $billCount - $settlementCount,
                    ]))],
                  ['.btn-settlement-all-delete', 'click', $rqSettlementFunc->delSettlements()
                    ->confirm(__('meeting.settlement.questions.delete-all', [
                      'count' => $settlementCount,
                    ]))],
                ])>
@include('tontine::parts.table.menu', [
  'btnSize' => '',
  'btnText' => __('common.labels.all'),
  'menus' => $menus,
])
                </div>
@endif
