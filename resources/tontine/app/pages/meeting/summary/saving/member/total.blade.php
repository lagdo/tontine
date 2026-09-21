@if (($fund->s_count ?? 0) > 0)
                      <div class="p-2 font-weight-bold float-right">
                        {{ $fund->s_count }} / {{ $locale->formatMoney($fund->s_amount) }}
                      </div>
@endif
