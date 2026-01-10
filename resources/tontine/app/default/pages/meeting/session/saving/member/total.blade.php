@if (($fund->s_count ?? 0) > 0)
                      <div class="font-weight-bold float-right">
                        <div>{{ $fund->s_count }}</div>
                        <div>{{ $locale->formatMoney($fund->s_amount) }}</div>
                      </div>
@endif
