              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col-auto">
                    <h2 class="section-title">{{ $tontine->name }} - {{ __('tontine.titles.rounds') }}</h2>
                  </div>
                  <div class="col">
                    <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                      {{-- <button type="button" class="btn btn-primary" id="btn-round-refresh"><i class="fa fa-sync"></i></button> --}}
                      <button type="button" class="btn btn-primary" id="btn-round-create"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="round-page">
                  </div> <!-- End table -->
                </div>
              </div>
