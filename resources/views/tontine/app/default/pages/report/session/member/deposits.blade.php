@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.deposits') }}</div>
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
@foreach($receivables as $receivable)
                        <tr>
                          <td>{{ $receivable->pool->title }}</td>
                          <td class="currency">{{ $receivable->pool->deposit_fixed ?
                            $locale->formatMoney($receivable->amount) : ($receivable->paid ?
                            $locale->formatMoney($receivable->deposit->amount) :
                            __('tontine.labels.types.libre')) }}</td>
                          <td class="table-item-menu"><i class="fa fa-toggle-{{ $receivable->paid ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
