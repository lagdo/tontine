@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.remitments') !!}</div>
                    </div>
@if ($hasAuctions)
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-remitment-auctions">{{ __('meeting.titles.auctions') }}</button>
                      </div>
                    </div>
@endif
@if($session->opened)
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-remitments-refresh"><i class="fa fa-sync"></i></button>
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
@foreach($pools as $pool)
@php
  $template = $session->disabled($pool) ? 'disabled' :
    ($session->closed ? 'closed' : ($session->pending ? 'pending' : 'opened'));
@endphp
                        @include('tontine.app.default.pages.meeting.pool.' . $template, [
                          'pool' => $pool,
                          'amount' => $pool->deposit_fixed ? $locale->formatMoney($pool->amount, true) :
                            __('tontine.labels.types.libre'),
                          'paid' => $pool->pay_paid,
                          'count' => $pool->pay_count,
                          'total' => $pool->amount_paid,
                          'menuClass' => 'btn-pool-remitments',
                        ])
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
