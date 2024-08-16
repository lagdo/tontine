@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.closings') !!}</div>
                    </div>
@if($session->opened)
                    <div class="col-auto">
                      <div class="btn-group float-right mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-closings-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
@if($session->opened)
                  <div class="row">
                    <div class="col">
                      &nbsp;
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! Form::select('fund_id', $funds, 0, ['class' => 'form-control',
                          'style' => 'height:36px; padding:5px 15px;', 'id' => 'closings-fund-id']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-fund-edit-closing"><i class="fa fa-circle-notch"></i></button>
                          <button type="button" class="btn btn-primary" id="btn-fund-show-savings"><i class="fa fa-percentage"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
@endif
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('tontine.fund.labels.fund') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($closings as $closing)
                        <tr>
                          <td>{!! $funds[$closing->fund_id] !!}</td>
                          <td class="currency">{{ $locale->formatMoney($closing->profit, true) }}</td>
                          <td class="table-item-menu">
@if($session->opened)
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $closing->fund_id,
  'menus' => [[
    'class' => 'btn-fund-edit-closing',
    'text' => __('common.actions.edit'),
  ], [
    'class' => 'btn-fund-show-savings',
    'text' => __('meeting.actions.savings'),
  ], [
    'class' => 'btn-closing-delete',
    'text' => __('common.actions.delete'),
  ]],
])
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
