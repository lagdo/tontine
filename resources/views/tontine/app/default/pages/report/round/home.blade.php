@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('sqids', 'Sqids\SqidsInterface')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('figures.titles.amounts') }} ({{ $locale->getCurrencyName() }})</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-meeting-report-refresh"><i class="fa fa-sync"></i></button>
                  <a type="button" class="btn btn-primary" target="_blank" href="{{
                    route('report.round', ['roundId' => $sqids->encode([$round->id])]) }}"><i class="fa fa-file-pdf"></i></a>
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
