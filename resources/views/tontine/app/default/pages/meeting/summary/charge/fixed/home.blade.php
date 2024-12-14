@php
  $rqFixedFeePage = rq(Ajax\App\Meeting\Summary\Charge\FixedFeePage::class);
@endphp
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.charge.titles.fixed') !!}</div>
                    </div>
                  </div>
                  <div class="table-responsive" @jxnBind($rqFixedFeePage)>
                    @jxnHtml($rqFixedFeePage)
                  </div> <!-- End table -->
