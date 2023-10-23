                  <div class="row align-items-center">
                    <div class="col">
                      <h2 class="section-title">{{ $pool->title }} - {{ __('tontine.pool.titles.members') }}</h2>
                    </div>
                    <div class="col-auto">
                      <div class="input-group ml-2 mb-2">
                        {!! Form::text('search', $search, ['class' => 'form-control', 'id' => 'txt-subscription-members-search']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-subscription-members-search"><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-subscription-members-filter"><i class="fa fa-filter"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-subscription-members-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>

                  <!-- Data tables -->
                  <div class="table-responsive" id="pool-subscription-members-page">
                  </div> <!-- End table -->
