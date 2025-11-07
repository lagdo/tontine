<div style="@if ($billCount === 0) padding: 5px;@endif text-align: right; line-height: 18px;">
  {{ $billCount }}/{{ $memberCount }}@if ($billCount > 0)<br/>{!! $locale->formatMoney($billAmount) !!}@endif
</div>
