@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
@if($session->opened)
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-saving-add"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-savings-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($savings as $saving)
                        <tr>
                          <td>{{ $saving->member->name }}<br/>{!! $saving->fund ?
                            $saving->fund->title : __('tontine.fund.labels.default') !!}</td>
                          <td class="currency">{{ $locale->formatMoney($saving->amount, true) }}</td>
                          <td class="table-item-menu">
@if($session->opened)
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-saving-id',
  'dataIdValue' => $saving->id,
  'menus' => [[
    'class' => 'btn-saving-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-saving-delete',
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
