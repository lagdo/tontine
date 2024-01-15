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
                              <th class="table-item-menu">@if (($session)){{
                                $memberCount - ($session->absents_count ?? 0) }} /<br/>{{ $memberCount }}@endif</th>
                            </tr>
                          </thead>
                          <tbody>
@foreach ($members as $member)
                            <tr>
                              <td>{{ $member->name }}</td>
                              <td class="table-item-menu">{{ $sessionCount - ($member->absences_count ?? 0) }} /<br/>{{ $sessionCount }}</td>
                              <td class="table-item-menu" data-member-id="{{ $member->id }}">
@if (!$session)
                                <button type="button" class="btn btn-primary btn-show-member-presences"><i class="fa fa-arrow-circle-right"></i></button>
@elseif ($session->opened)
                                <a href="javascript:void(0)" class="btn-toggle-member-presence"><i class="fa fa-toggle-{{
                                  $absences->has($member->id) ? 'off' : 'on' }}"></i></a>
@else
                                <i class="fa fa-toggle-{{ $absences->has($member->id) ? 'off' : 'on' }}"></i>
@endif
                              </td>
                            </tr>
@endforeach
                          </tbody>
                        </table>
                        <nav>{!! $pagination !!}</nav>
