@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $selectFundId = Jaxon\pm()->select('closings-fund-id')->toInt();
  $rqSaving = Jaxon\rq(Ajax\App\Meeting\Summary\Saving\Saving::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.closings') !!}</div>
                    </div>
                  </div>
@if($session->opened)
                  <div class="row">
                    <div class="col">
                      &nbsp;
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! $htmlBuilder->select('fund_id', $funds, 0)->id('closings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqSaving->fund($selectFundId))><i class="fa fa-percentage"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
@endif
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.closing.labels.fund') !!}</th>
                          <th class="currency"></th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($closings as $closing)
                        <tr>
                          <td>{!! $funds[$closing->fund_id] !!}</td>
                          <td class="currency">
                            {!! $closing->title !!}@if( $closing->is_round ) <br/>{{
                              $locale->formatMoney($closing->profit, true) }}@endif
                          </td>
                          <td class="table-item-menu">&nbsp;</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
