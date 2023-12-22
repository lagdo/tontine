          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.titles.tontines') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-tontine-refresh"><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-tontine-create"><i class="fa fa-plus"></i></button>
                </div>
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-show-select">
                    <i class="fa fa-check-square"></i> {{ __('tontine.actions.choose') }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body">
              <div class="table-responsive" id="tontine-page">
                @isset($tontines) @include('tontine.app.default.pages.tontine.page') @endisset
              </div> <!-- End table -->
            </div>
          </div>
