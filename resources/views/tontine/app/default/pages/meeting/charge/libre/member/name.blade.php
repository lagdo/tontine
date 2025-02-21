@inject('locale', 'Siak\Tontine\Service\LocaleService')
{{ $member->name }}@if ($member->remaining > 0)<br/>{{ __('meeting.target.labels.remaining',
  ['amount' => $locale->formatMoney($member->remaining, true)]) }}@endif
