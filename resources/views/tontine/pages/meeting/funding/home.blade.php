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
                          <th>{!! __('common.labels.amount') !!}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($fundings as $funding)
                        <tr>
                          <td>{{ $funding->member->name }}</td>
                          <td>{{ $funding->amount }}</td>
                          <td class="table-item-menu" data-funding-id="{{ $funding->id }}">
                            <a href="javascript:void(0)" class="btn-funding-delete"><i class="fa fa-times-circle"></i></a>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
