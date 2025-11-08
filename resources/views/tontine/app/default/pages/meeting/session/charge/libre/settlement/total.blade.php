@if ($billCount > 0)
<div style="@if ($settlementCount === 0) padding: 5px;@endif text-align: right; line-height: 18px;">
  <div>{{ $settlementCount }}/{{ $billCount }}</div>
@if ($settlementCount > 0)
  <div>{!! $locale->formatMoney($settlementAmount) !!}</div>
@endif
</div>
@endif
