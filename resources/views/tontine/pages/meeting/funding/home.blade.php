@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.titles.fundings') }}</div>
                    </div>
@if($session->opened)
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-funding-add"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-fundings-refresh"><i class="fa fa-sync"></i></button>
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
@foreach ($fundings as $funding)
                        <tr>
                          <td>{{ $funding->member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($funding->amount, true) }}</td>
                          <td class="table-item-menu">
@if($session->opened)
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-funding-id',
  'dataIdValue' => $funding->id,
  'menus' => [[
    'class' => 'btn-funding-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-funding-delete',
    'text' => __('common.actions.delete'),
  ]],
])
@else
                            <i class="fa fa-trash-alt"></i>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
