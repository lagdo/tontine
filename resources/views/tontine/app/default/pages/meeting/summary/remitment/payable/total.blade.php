@inject('locale', 'Siak\Tontine\Service\LocaleService')
{{ $locale->formatMoney($amount) }} / {{ $count }} / {{ $locale->formatMoney($total) }}
