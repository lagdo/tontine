          <div class="row">
            <div class="col-md-6 col-sm-12" id="payment-members-home">
              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.menus.payments') }}</h2>
                  </div>
                  <div class="col-auto" id="payment-settings">
                  </div>
                  <div class="col-auto">
                    @if ($sessions->count() > 0){{ Form::select('session_id', $sessions, 0,
                      ['class' => 'form-control', 'id' => 'select-session']) }}@endif
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('tontine.menus.members') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive" id="payment-members-page">
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 payment-sm-hide" id="member-payables-home">
            </div>
          </div>
