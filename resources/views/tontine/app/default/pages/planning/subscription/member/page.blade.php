              <div class="table-responsive">
                <table class="table table-bordered">
                   <thead>
                    <tr>
                      <th>
                        <div class="input-group">
                          {!! Form::text('search', $search, ['class' => 'form-control', 'id' => 'txt-subscription-members-search']) !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btn-subscription-members-search"><i class="fa fa-search"></i></button>
                          </div>
                        </div>
                      </th>
                      <th style="text-align: center">{{ $total }}</th>
                    </tr>
                  </thead>
                 <tbody>
@foreach ($members as $member)
                    <tr>
                      <td>{{ $member->name }}</td>
                      <td class="table-member-subscription">
                        <div class="input-group float-right" data-member-id="{{ $member->id }}" style="width:auto;">
                          <div class="input-group-prepend">
                            <button type="button" class="btn btn-primary btn-sm btn-subscription-member-del"><i class="fas fa-minus"></i></button>
                          </div>
                          <span class="input-group-text">{{ $member->subscriptions_count }}</span>
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-sm btn-subscription-member-add"><i class="fas fa-plus"></i></button>
                          </div>
                        </div>
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
              </div>
