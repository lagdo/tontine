@php
  $rqHeader = rq(Ajax\App\Report\Session\Header::class);
  $rqSelect = rq(Ajax\App\Report\Session\Select::class);
  $rqSessionTables = rq(Ajax\App\Report\Session\SessionTables::class);
@endphp
          <div class="section-body">
            <div class="row mb-2" @jxnBind($rqHeader)>
            </div>
            <div class="row" @jxnBind($rqSelect)>
            </div>
          </div>

          <div @jxnBind($rqSessionTables)>
          </div>
