@if ($memberCount > 0)
@php
  $rqMemberFunc = rq(Ajax\App\Meeting\Session\Charge\Libre\MemberFunc::class);
  $chargeType = $charge->is_fee ? 'fees' : 'fines';
  $menus = [];
  if($noBillCount > 0)
  {
    $menus[] = [
      'class' => 'btn-bill-all-create',
      'text' => __('common.actions.add'),
    ];
  }
  if($noBillCount < $memberCount)
  {
    $menus[] = [
      'class' => 'btn-bill-all-delete',
      'text' => __('common.actions.delete'),
    ];
  }
@endphp
                <div class="btn-group ml-2" role="group" @jxnEvent([
                  ['.btn-bill-all-create', 'click', $rqMemberFunc->confirmAll()],
                  ['.btn-bill-all-delete', 'click', $rqMemberFunc->deleteBills()
                    ->confirm(__('meeting.bill.questions.delete-all', [
                      'items' => strtolower(__("meeting.titles.$chargeType"))
                    ]))],
                ])>
@include('tontine::parts.table.menu', [
  'btnSize' => '',
  'btnText' => __('common.labels.all'),
  'menus' => $menus,
])
                </div>
@endif
