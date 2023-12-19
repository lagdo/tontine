@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.remitments') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
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
                            {{ $locale->formatMoney($payable->amount, true) }}
@isset ($auctions[$payable->id])
                            <br/>{{ __('meeting.remitment.labels.auction') }}: {{ $locale->formatMoney($auctions[$payable->id]->amount) }}
@endisset
                          </td>
                          <td class="table-item-menu">
                            <i class="fa fa-toggle-{{ $payable->paid ? 'on' : 'off' }}"></i>
@isset ($auctions[$payable->id])
                            <br/><i class="fa fa-toggle-{{ $auctions[$payable->id]->paid ? 'on' : 'off' }}"></i>
@endisset
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
