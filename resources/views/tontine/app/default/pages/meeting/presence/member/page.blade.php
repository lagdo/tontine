@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
                            <div class="input-group">
                              {!! Form::text('search', $search, ['class' => 'form-control', 'id' => 'txt-presence-members-search']) !!}
                              <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="btn-presence-members-search"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
                          </th>
                          <th class="table-item-menu"></th>
                          <th class="table-item-menu">{{ __('tontine.labels.present') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="table-item-menu">{{ $sessionCount - ($member->absences_count ?? 0) }} /<br/>{{ $sessionCount }}</td>
                          <td class="table-item-menu" data-member-id="{{ $member->id }}">
@if ($session->opened)
                            <a href="javascript:void(0)" class="btn-toggle-member-presence"><i class="fa fa-toggle-{{
                              $absences->has($member->id) ? 'off' : 'on' }}"></i></a>
@else
                            @if ($session->pending)&nbsp; @else <i class="fa fa-toggle-{{
                              $absences->has($member->id) ? 'off' : 'on' }}"></i>@endif
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
