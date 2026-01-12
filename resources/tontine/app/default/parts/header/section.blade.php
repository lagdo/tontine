              <div class="row section-header-title">
                <div class="col">
                  <h2>{{ $section }}</h2><span style="font-weight:700; font-size:17px;"> > {{ $entry }}</span>
                </div>
                <div class="col-auto d-flex align-items-end">
@if ($currentGuild !== null)
                  <span style="font-weight:700; font-size:17px;">{{ $locale->getCurrencyName() }} <i class="fa fa-money-bill"></i></span>
@endif
                </div>
              </div>
