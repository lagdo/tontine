@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.outflows') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>&nbsp;</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($outflows as $outflow)
                        <tr>
                          <td>{{ $outflow->category->name }}<br/>{{ $outflow->session->title }}</td>
                          <td class="currency">{{ $locale->formatMoney($outflow->amount) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
