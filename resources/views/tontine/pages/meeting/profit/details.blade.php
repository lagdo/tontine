@inject('locale', 'Siak\Tontine\Service\LocaleService')
                      {!! __('meeting.profit.distribution.amount', ['amount' => $locale->formatMoney($profitAmount, true)]) !!}
@if ($distributionSum > 0)
                      {!! __('meeting.profit.distribution.parts', ['parts' => $distributionSum]) !!}
@if ($distributionCount > 1)
                      {!! __('meeting.profit.distribution.basis', ['unit' => $locale->formatMoney($partUnitValue, true)]) !!}
@endif
@endif
