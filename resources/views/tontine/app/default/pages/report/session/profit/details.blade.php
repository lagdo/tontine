@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <div class="col">
                      {!! __('meeting.profit.distribution.total', [
                          'saving' => $locale->formatMoney($amounts['saving'], true),
                          'refund' => $locale->formatMoney($amounts['refund'], true),
                        ]) !!}
                    </div>
                    <div class="col-auto">
                      {!! __('meeting.profit.distribution.amount', ['amount' => $locale->formatMoney($profitAmount, true)]) !!}
@if ($distributionSum > 0)
                      {!! __('meeting.profit.distribution.parts', ['parts' => $distributionSum]) !!}
@if ($distributionCount > 1)
                      {!! __('meeting.profit.distribution.basis', ['unit' => $locale->formatMoney($partUnitValue, true)]) !!}
@endif
@endif
                    </div>
