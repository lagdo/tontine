              <div class="table-responsive">
                <table class="table table-bordered">
                   <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th style="text-align: center">{{ $count }}</th>
                    </tr>
                  </thead>
                 <tbody>
@foreach ($members as $member)
                    <tr>
                      <td>{{ $member->name }}</td>
                      <td class="table-member-subscription" data-member-id="{{ $member->id }}">
@if ($tontine->is_libre)
@if ($member->subscriptions_count > 0)
                        <a href="javascript:void(0)" class="btn-subscription-del"><i class="fa fa-toggle-on"></i></a>
@else
                        <a href="javascript:void(0)" class="btn-subscription-add"><i class="fa fa-toggle-off"></i></a>
@endif
@else
                        <div class="input-group float-right" style="width:auto;">
                          <div class="input-group-prepend">
                            <button type="button" class="btn btn-primary btn-sm btn-subscription-del"><i class="fas fa-minus"></i></button>
                          </div>
                          <span class="input-group-text">{{ $member->subscriptions_count }}</span>
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-sm btn-subscription-add"><i class="fas fa-plus"></i></button>
                          </div>
                        </div>
@endif
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
              </div>
