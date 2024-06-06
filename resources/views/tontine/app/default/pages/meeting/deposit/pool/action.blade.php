@if (!$session->closed)
@if ($depositCount < $receivableCount)
<a href="javascript:void(0)" class="btn-add-all-deposits"><i class="fa fa-toggle-off"></i></a>
@else
<a href="javascript:void(0)" class="btn-del-all-deposits"><i class="fa fa-toggle-on"></i></a>
@endif
@else
<i class="fa fa-toggle-{{ $depositCount < $receivableCount ? 'off' : 'on' }}"></i>
@endif
