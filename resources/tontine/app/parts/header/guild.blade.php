@php
  $rqGuildMenu = rq(Ajax\Page\Header\GuildMenuFunc::class);
  $rqRoundMenu = rq(Ajax\Page\Header\RoundMenuFunc::class);
@endphp
              <div class="row">
                <div class="col-auto">
                  <h2>
                    @if($guildCount > 0)<a class="highlight" role="link" @jxnClick($rqGuildMenu
                      ->showGuilds())><i class="fa fa-caret-square-right"></i></a>@endif
                    {{ $currentGuild?->name ?? __('tontine.titles.select.guild') }}
                  </h2>
                </div>
                <div class="col d-flex align-items-end">
@if ($currentGuild !== null)
                  <h2 class="ml-auto">
                    @if($roundCount > 0)<a class="highlight" role="link" @jxnClick($rqRoundMenu
                      ->showRounds())><i class="fa fa-caret-square-right"></i></a>@endif
                    {{ $currentRound?->title ?? __('tontine.titles.select.round') }}
                  </h2>
@endif
                </div>
              </div>
