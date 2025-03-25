@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <div class="col">
                      {!! __('meeting.profit.distribution.total', [
                          'saving' => $locale->formatMoney($amounts['saving']),
                          'refund' => $locale->formatMoney($amounts['refund']),
                      ]) !!}
                    </div>
                    <div class="col-auto">
                      {!! __('meeting.profit.distribution.amount', [
                        'amount' => $locale->formatMoney($profitAmount),
                      ]) !!}
@if ($distribution->rewarded->count() > 0)
                      {!! __('meeting.profit.distribution.parts', [
                        'parts' => $distribution->savings->sum('parts'),
                      ]) !!}
@if ($distribution->rewarded->count() > 1)
                      {!! __('meeting.profit.distribution.basis', [
                        'unit' => $locale->formatMoney($distribution->partAmount),
                      ]) !!}
@endif
@endif
                    </div>
