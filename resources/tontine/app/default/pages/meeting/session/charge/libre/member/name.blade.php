<div>{{ $member->name }}</div>
@if ($member->remaining > 0)
<div>{{ __('meeting.target.labels.remaining',
  ['amount' => $locale->formatMoney($member->remaining)]) }}</div>
@endif
