@php
  $rqOutflowPage = rq(Ajax\App\Meeting\Summary\Cash\OutflowPage::class);
@endphp
                    <div class="table-responsive" id="content-session-outflows">
                      <table class="table table-bordered responsive">
                        <thead>
                          <tr>
                            <th>{!! __('meeting.labels.category') !!}</th>
                            <th>{!! __('meeting.labels.member') !!}</th>
                            <th>{!! __('meeting.labels.charge') !!}</th>
                            <th class="currency">{!! __('common.labels.amount') !!}</th>
                            <th class="table-item-menu">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>
@foreach ($outflows as $outflow)
                          <tr>
                            <td>
                              <div>{{ $outflow->category->name }}</div>
@if ($outflow->comment)
                              <div>{{ $outflow->comment }}</div>
@endif
                            </td>
                            <td>{{ $outflow->member?->name ?? '' }}</td>
                            <td>{{ $outflow->charge?->name ?? '' }}</td>
                            <td class="currency">{{ $locale->formatMoney($outflow->amount) }}</td>
                            <td class="table-item-menu">&nbsp;</td>
                          </tr>
@endforeach
                        </tbody>
                      </table>
                      <nav @jxnPagination($rqOutflowPage)>
                      </nav>
                    </div> <!-- End table -->
