@php
  $rqLibreFeePage = rq(Ajax\App\Meeting\Summary\Charge\LibreFeePage::class);
@endphp
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.charge.titles.variable') !!}</div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-summary-fees-libre-page" @jxnBind($rqLibreFeePage)>
                    @jxnHtml($rqLibreFeePage)
                  </div> <!-- End table -->
