<div @if ($billCount === 0)style="padding: 8px 5px;"@endif>
  {{ $billCount }}/{{ $memberCount }}@if ($billCount > 0)<br/>{!! $locale->formatMoney($billAmount) !!}@endif
</div>
