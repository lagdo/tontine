@if ($billCount > 0)
<div style="@if ($settlementCount === 0) padding: 5px;@endif text-align: right;">
  {{ $settlementCount }}/{{ $billCount }}@if ($settlementCount > 0)<br/>{!! $locale->formatMoney($settlementAmount) !!}@endif
</div>
@endif
