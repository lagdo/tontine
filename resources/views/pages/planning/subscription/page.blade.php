              <div class="table-responsive">
                <table class="table table-bordered">
                   <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th>&nbsp;</th>
                    </tr>
                  </thead>
                 <tbody>
@foreach ($members as $member)
                    <tr>
                      <td>{{ $member->gender === 'F' ? 'Mme' : 'M.' }} {{ $member->name }}</td>
                      <td class="table-member-subscription">
                        <div class="input-group float-right" data-member-id="{{ $member->id }}" style="width:auto;">
                          <div class="input-group-prepend">
                            <button type="button" class="btn btn-primary btn-sm btn-subscription-del"><i class="fas fa-minus"></i></button>
                          </div>
                          <span class="input-group-text">{{ $member->subscriptionCount }}</span>
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-sm btn-subscription-add"><i class="fas fa-plus"></i></button>
                          </div>
                        </div>
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
              </div>
