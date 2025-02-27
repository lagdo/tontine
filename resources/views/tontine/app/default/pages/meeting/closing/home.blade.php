@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $closingFundId = pm()->select('closings-fund-id')->toInt();
  $selectFundId = jq()->parent()->attr('data-fund-id')->toInt();
  $rqClosing = rq(Ajax\App\Meeting\Session\Saving\Closing::class);
  $rqClosingFunc = rq(Ajax\App\Meeting\Session\Saving\ClosingFunc::class);
  $rqFundReport = rq(Ajax\App\Report\Session\Saving\Fund::class);
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
                        {!! $html->select('fund_id', $funds, 0)->id('closings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqFundReport->fund($closingFundId))><i class="fa fa-percentage"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="dropdown float-right">
                        <button class="btn btn-primary dropdown-toggle" type="button"data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-circle-notch"></i> {{ __('meeting.saving.actions.close') }}
                        </button>
                        <div class="dropdown-menu">
                          <button type="button" class="dropdown-item" @jxnClick($rqClosingFunc->editRoundClosing($closingFundId))>{!! __('meeting.saving.actions.saving') !!}</button>
                          <button type="button" class="dropdown-item" @jxnClick($rqClosingFunc->editInterestClosing($closingFundId))>{{ __('meeting.saving.actions.interest') }}</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-session-closings" @jxnTarget()>
                    <div @jxnEvent(['.btn-fund-edit-round-closing', 'click'], $rqClosingFunc->editRoundClosing($selectFundId))></div>
                    <div @jxnEvent(['.btn-fund-edit-interest-closing', 'click'], $rqClosingFunc->editInterestClosing($selectFundId))></div>
                    <div @jxnEvent(['.btn-fund-delete-round-closing', 'click'], $rqClosingFunc->deleteRoundClosing($selectFundId)
                      ->confirm(trans('meeting.closing.questions.delete')))></div>
                    <div @jxnEvent(['.btn-fund-delete-interest-closing', 'click'], $rqClosingFunc->deleteInterestClosing($selectFundId)
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
@include('tontine::parts.table.menu', [
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
