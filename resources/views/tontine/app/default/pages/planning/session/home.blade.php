              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.titles.sessions') }}@if ($round !== null) :: {{ $round->title }}@endif</h2>
                  </div>
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" id="btn-rounds-back"><i class="fa fa-arrow-left"></i></button>
                  </div>
@if ($round !== null)
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" id="btn-sessions-refresh"><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" id="btn-sessions-add"><i class="fa fa-plus"></i></button>
                      <button type="button" class="btn btn-primary" id="btn-sessions-add-list"><i class="fa fa-list"></i></button>
                    </div>
                  </div>
@endif
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="content-page-sessions">
                  </div> <!-- End table -->
                </div>
              </div>
