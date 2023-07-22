@inject('locale', 'Siak\Tontine\Service\LocaleService')
                        {!! Form::text('amount', !$amount ? '': $locale->formatMoney($amount, true),
                          ['class' => 'form-control', 'readonly' => 'readonly', 'style' => 'text-align:right']) !!}
