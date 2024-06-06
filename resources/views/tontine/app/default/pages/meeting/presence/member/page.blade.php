@inject('locale', 'Siak\Tontine\Service\LocaleService')
                        <table class="table table-bordered responsive">
                          <thead>
                            <tr>
                              <th>{{ __('common.labels.name') }}</th>
                              <th class="table-item-toggle"></th>
                              <th class="table-item-menu"></th>
                            </tr>
                          </thead>
                          <tbody>
@foreach ($members as $member)
                            <tr>
                              <td>{{ $member->name }}</td>
                              <td class="table-item-toggle">{{ $sessionCount - ($member->absences_count ?? 0) }}/{{ $sessionCount }}</td>
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
