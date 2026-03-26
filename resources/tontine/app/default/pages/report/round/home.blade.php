@inject('sqids', 'Sqids\SqidsInterface')
@php
  $rqHeader = rq(Ajax\App\Report\Round\Header::class);
  $rqSelect = rq(Ajax\App\Report\Round\Select::class);
  $rqRoundTables = rq(Ajax\App\Report\Round\RoundTables::class);
@endphp
          <div class="section-body">
            <div class="row mb-2" @jxnBind($rqHeader)>
            </div>
            <div class="row mb-2" @jxnBind($rqSelect)>
            </div>
          </div>

          <div @jxnBind($rqRoundTables)>
          </div>
