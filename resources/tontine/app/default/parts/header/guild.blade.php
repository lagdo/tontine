@php
  $rqMenuFunc = rq(Ajax\Page\Header\MenuFunc::class);
@endphp
              <div class="row">
                <div class="col-auto">
                  <h2>
                    @if($guildCount > 0)<a class="highlight" role="link" @jxnClick($rqMenuFunc
                      ->showGuilds())><i class="fa fa-caret-square-right"></i></a>@endif
                    {{ $guild?->name ?? __('tontine.titles.select.guild') }}
                  </h2>
                </div>
                <div class="col d-flex align-items-end">
                  <h2 class="ml-auto">
                    @if($roundCount > 0)<a class="highlight" role="link" @jxnClick($rqMenuFunc
                      ->showRounds())><i class="fa fa-caret-square-right"></i></a>@endif
                    {{ $round?->title ?? __('tontine.titles.select.round') }}
                  </h2>
                </div>
              </div>
