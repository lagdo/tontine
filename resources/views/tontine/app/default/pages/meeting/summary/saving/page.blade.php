@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqSavingPage = rq(Ajax\App\Meeting\Summary\Saving\SavingPage::class);
@endphp
                  <div class="table-responsive" id="content-summary-savings-page">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="table-item-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($savings as $saving)
                        <tr>
                          <td>{{ $saving->member }}<br/>{!! $saving->fund ?
                            $saving->fund->title : __('tontine.fund.labels.default') !!}</td>
                          <td class="currency">{{ $locale->formatMoney($saving->amount, true) }}</td>
                          <td class="table-item-menu">&nbsp;</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSavingPage)>
                    </nav>
                  </div> <!-- End table -->
