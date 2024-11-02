@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $closingFundId = Jaxon\pm()->select('closings-fund-id')->toInt();
  $selectFundId = Jaxon\jq()->parent()->attr('data-fund-id')->toInt();
  $rqClosing = Jaxon\rq(App\Ajax\Web\Meeting\Session\Saving\Closing::class);
  $rqSavingReport = Jaxon\rq(App\Ajax\Web\Report\Session\Saving\Fund::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.closings') !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqClosing->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      &nbsp;
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! $htmlBuilder->select('fund_id', $funds, 0)->id('closings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqClosing->editRoundClosing($closingFundId))><i class="fa fa-circle-notch"></i></button>
                          <button type="button" class="btn btn-primary" @jxnClick($rqClosing->editInterestClosing($closingFundId))><i class="far fa-stop-circle"></i></button>
                          <button type="button" class="btn btn-primary" @jxnClick($rqSavingReport->fund($closingFundId, 'session'))><i class="fa fa-percentage"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" @jxnTarget()>
                    <div @jxnOn(['.btn-fund-edit-round-closing', 'click', ''], $rqClosing->editRoundClosing($selectFundId))></div>
                    <div @jxnOn(['.btn-fund-edit-interest-closing', 'click', ''], $rqClosing->editInterestClosing($selectFundId))></div>
                    <div @jxnOn(['.btn-fund-delete-round-closing', 'click', ''], $rqClosing->deleteRoundClosing($selectFundId)
                      ->confirm(trans('meeting.closing.questions.delete')))></div>
                    <div @jxnOn(['.btn-fund-delete-interest-closing', 'click', ''], $rqClosing->deleteInterestClosing($selectFundId)
                      ->confirm(trans('meeting.closing.questions.delete')))></div>

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
                          <td class="table-item-menu">
@php
  $label = $closing->label;
@endphp
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $closing->fund_id,
  'menus' => [[
    'class' => 'btn-fund-edit-' . $label . '-closing',
    'text' => __('common.actions.edit'),
  ], [
    'class' => 'btn-fund-delete-' . $label . '-closing',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
