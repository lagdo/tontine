@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqMemberPage = rq(Ajax\App\Meeting\Summary\Saving\MemberPage::class);
@endphp
                  <div class="table-responsive" id="content-session-saving-members" @jxnTarget()>
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($member->saving?->amount ?? 0, false, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqMemberPage)>
                    </nav>
                  </div> <!-- End table -->
