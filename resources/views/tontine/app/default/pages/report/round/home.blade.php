@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('sqids', 'Sqids\SqidsInterface')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('figures.titles.amounts') }} ({{ $locale->getCurrencyName() }})</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-1" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-meeting-report-refresh"><i class="fa fa-sync"></i></button>
                </div>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-1">
                  <button type="button" class="btn btn-primary" id="btn-tontine-options"><i class="fa fa-cog"></i></button>
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-pdf"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ route('report.round',
                      ['roundId' => $sqids->encode([$round->id])]) }}">{{ __('tontine.report.actions.round') }}</a>
                  </div>
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
