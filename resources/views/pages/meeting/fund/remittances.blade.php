                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.remittances') !!}</div>
                    </div>
@if($session->opened)
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-remittances-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($funds as $fund)
@if($session->disabled($fund))
                        @include('pages.meeting.fund.disabled', [
                            'fund' => $fund,
                        ])
@elseif($session->opened)
                        @include('pages.meeting.fund.opened', [
                            'fund' => $fund,
                            'paid' => $fund->pay_paid,
                            'count' => $fund->pay_count,
                            'tontine' => $tontine,
                            'menuClass' => 'btn-fund-remittances',
                            'menuText' => __('meeting.actions.remittances'),
                        ])
@elseif($session->closed)
                        @include('pages.meeting.fund.closed', [
                            'fund' => $fund,
                            'paid' => $fund->pay_paid,
                            'count' => $fund->pay_count,
                            'summary' => $summary['payables'],
                        ])
@else
                        @include('pages.meeting.fund.pending', [
                            'fund' => $fund,
                            'paid' => $fund->pay_paid,
                            'count' => $fund->pay_count,
                        ])
@endif
@endforeach
@if($session->closed)
                        <tr>
                          <td colspan="2">{!! __('common.labels.total') !!}</td>
                          <td>{{ $summary['sum']['payables'] }}</td>
                        </tr>
@endif
                      </tbody>
                    </table>
                  </div> <!-- End table -->
