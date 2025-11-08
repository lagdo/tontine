@php
  $fundId = jq()->parent()->attr('data-fund-id')->toInt();
  $rqSavingPage = rq(Ajax\App\Meeting\Summary\Saving\SavingPage::class);
  $rqMember = rq(Ajax\App\Meeting\Summary\Saving\Member::class);
@endphp
                  <div class="table-responsive" id="content-session-funds-page" @jxnEvent([
                    ['.btn-fund-savings', 'click', $rqMember->fund($fundId)]])>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($funds as $fund)
                        <tr>
                          <td>{!! $fund->title !!}</td>
                          <td class="currency">
                            <div>{{ $fund->s_count ?? 0 }}</div>
                            <div>{{ $locale->formatMoney($fund->s_amount ?? 0) }}</div>
                          </td>
                          <td class="table-item-menu" data-fund-id="{{ $fund->id }}">
                            <button type="button" class="btn btn-primary btn-fund-savings"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSavingPage)>
                    </nav>
                  </div> <!-- End table -->
