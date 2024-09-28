@if ($charge->is_active && !$charge->is_variable)
@if (!$session->closed)
@if ($settlementCount < $billCount)
<a role="link" class="btn-add-all-settlements"><i class="fa fa-toggle-off"></i></a>
@elseif ($billCount > 0)
<a role="link" class="btn-del-all-settlements"><i class="fa fa-toggle-on"></i></a>
@endif
@else
<i class="fa fa-toggle-{{ $settlementCount < $billCount ? 'off' : 'on' }}"></i>
@endif
@endif
