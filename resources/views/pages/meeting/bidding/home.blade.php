                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.titles.biddings') }}@isset($fund) - {{ $fund->title }}@endisset</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-biddings-back"><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>{!! __('common.labels.amount') !!}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($biddings as $bidding)
                        <tr>
                          <td>{{ $bidding->title }}</td>
                          <td>{{ $bidding->amount }}</td>
                          <td class="table-item-menu">
@include('parts.table.menu', [
  'dataIdKey' => 'data-payable-id',
  'dataIdValue' => $bidding->id,
  'menus' => $bidding->available ? [[
    'class' => 'btn-bidding-add',
    'text' => __('common.actions.add'),
  ]] : [[
    'class' => 'btn-bidding-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
