@if (!$session->closed)
@if ($depositCount < $receivableCount)
<a role="link" class="btn-add-all-deposits"><i class="fa fa-toggle-off"></i></a>
@else
<a role="link" class="btn-del-all-deposits"><i class="fa fa-toggle-on"></i></a>
@endif
@else
<i class="fa fa-toggle-{{ $depositCount < $receivableCount ? 'off' : 'on' }}"></i>
@endif
