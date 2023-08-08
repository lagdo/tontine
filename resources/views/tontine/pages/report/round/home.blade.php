@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('figures.titles.amounts') }} ({{ $locale->getCurrencyName() }})</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-meeting-report-refresh"><i class="fa fa-sync"></i></button>
                  {{-- <a type="button" class="btn btn-primary" target="_blank" href="{{ route('report.pool', ['poolId' => $pool->id]) }}"><i class="fa fa-file-pdf"></i></a> --}}
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-pools">
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-amounts">
            </div>
          </div>
