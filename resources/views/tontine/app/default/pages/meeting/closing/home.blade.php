@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.closings') !!}</div>
                    </div>
@if($session->opened)
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! Form::select('fund_id', $funds, 0, ['class' => 'form-control',
                          'style' => 'height:36px; padding:5px 15px;', 'id' => 'closings-fund-id']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-closing-edit"><i class="fa fa-plus"></i></button>
                          <button type="button" class="btn btn-primary" id="btn-fund-savings-show"><i class="fa fa-list"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-closings-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('tontine.fund.labels.fund') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($closings as $fundId => $amount)
                        <tr>
                          <td>{!! $funds[$fundId] !!}</td>
                          <td class="currency">{{ $locale->formatMoney($amount, true) }}</td>
                          <td class="table-item-menu">
@if($session->opened)
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $fundId,
  'menus' => [[
    'class' => 'btn-closing-edit',
    'text' => __('common.actions.edit'),
  ], [
    'class' => 'btn-fund-savings-show',
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
