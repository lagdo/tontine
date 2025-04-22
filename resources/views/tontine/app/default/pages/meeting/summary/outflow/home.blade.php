@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.outflows') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-summary-outflows">
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
                          <td>{{ $outflow->category->name }}@if (($outflow->comment)) <br/>{{
                            $outflow->comment }}@endif</td>
                          <td>@if (($outflow->member)) {{ $outflow->member->name }}@endif</td>
                          <td>@if (($outflow->charge)) {{ $outflow->charge->name }}@endif</td>
                          <td class="currency">{{ $locale->formatMoney($outflow->amount) }}</td>
                          <td class="table-item-menu">&nbsp;</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
