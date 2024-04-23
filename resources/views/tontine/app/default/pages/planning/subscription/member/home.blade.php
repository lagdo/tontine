                  <div class="row">
                    <div class="col">
                      <h2 class="section-title">{{ __('tontine.pool.titles.members') }}</h2>
                    </div>
@if ($pool->remit_planned)
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-subscription-beneficiaries">{{
                          __('tontine.subscription.titles.beneficiaries') }}</i></button>
                      </div>
                    </div>
@endif
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-subscription-members-filter"><i class="fa fa-filter"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-subscription-members-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">&nbsp;</div>
                    <div class="col-auto">
                      <div class="input-group">
                        {!! Form::text('search', '', ['class' => 'form-control', 'id' => 'txt-subscription-members-search']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-subscription-members-search"><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Data tables -->
                  <div class="table-responsive" id="pool-subscription-members-page">
                  </div> <!-- End table -->
