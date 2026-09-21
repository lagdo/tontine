<div style="@if ($billCount === 0) padding: 5px;@endif text-align: right; line-height: 18px;">
  <div>{{ $billCount }}/{{ $memberCount }}</div>
@if ($billCount > 0)
  <div>{!! $locale->formatMoney($billAmount) !!}</div>
@endif
</div>
