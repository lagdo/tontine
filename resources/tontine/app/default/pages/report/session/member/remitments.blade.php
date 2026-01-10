                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.remitments') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="table-item-menu">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($payables as $payable)
                        <tr>
                          <td>{{ $payable->pool->title }}</td>
                          <td class="currency">
                            <div>{{ $locale->formatMoney($payable->amount) }}</div>
@isset ($auctions[$payable->id])
                            <div>{{ __('meeting.remitment.labels.auction') }}: {{
                              $locale->formatMoney($auctions[$payable->id]->amount) }}</div>
@endisset
                          </td>
                          <td class="table-item-menu">
                            <div><i class="fa fa-toggle-{{ $payable->paid ? 'on' : 'off' }}"></i></div>
@isset ($auctions[$payable->id])
                            <div><i class="fa fa-toggle-{{ $auctions[$payable->id]->paid ? 'on' : 'off' }}"></i></div>
@endisset
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
