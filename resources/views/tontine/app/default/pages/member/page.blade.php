              <div class="table-responsive">
                <table class="table table-bordered">
                   <thead>
                    <tr>
                      <th>
                        <div class="input-group">
                          {!! Form::text('search', $search, ['class' => 'form-control', 'id' => 'txt-member-search']) !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btn-member-search"><i class="fa fa-search"></i></button>
                          </div>
                        </div>
                      </th>
                      <th>{!! __('common.labels.email') !!}</th>
                      <th>{!! __('common.labels.phone') !!}</th>
                      <th>{!! __('common.labels.active') !!}</th>
                      <th class="table-menu"></th>
                    </tr>
                  </thead>
                 <tbody>
@foreach ($members as $member)
                    <tr>
                      <td>{{ $member->name }}</td>
                      <td>{{ $member->email }}</td>
                      <td>{{ $member->phone }}</td>
                      <td class="table-item-menu" data-member-id="{{ $member->id }}">
                        <a href="javascript:void(0)" class="btn-member-toggle"><i class="fa fa-toggle-{{ $member->active ? 'on' : 'off' }}"></i></a>
                      </td>
                      <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-member-id',
  'dataIdValue' => $member->id,
  'menus' => [[
    'class' => 'btn-member-edit',
    'text' => __('common.actions.edit'),
   ],[
    'class' => 'btn-member-delete',
    'text' => __('common.actions.delete'),
  ]],
  'links' => [],
])
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
              </div>
