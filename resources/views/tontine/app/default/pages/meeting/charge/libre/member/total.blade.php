@inject('locale', 'Siak\Tontine\Service\LocaleService')
@if ($settlementCount > 0)<br/>({{ $settlementCount }} - {!! $locale->formatMoney($settlementAmount, true) !!})@endif