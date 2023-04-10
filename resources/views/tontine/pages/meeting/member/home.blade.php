          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.menus.members') }}</h2>
              </div>
              <div class="col-auto">
                {!! Form::select('member_id', $members, 0, ['class' => 'form-control', 'id' => 'select-member']) !!}
              </div>
              <div class="col-auto">
                {!! Form::select('session_id', $sessions, 0, ['class' => 'form-control', 'id' => 'select-session']) !!}
              </div>
              <div class="col-auto">
                <div class="input-group-append">
                  <button type="button" class="btn btn-primary" id="btn-member-select"><i class="fa fa-arrow-right"></i></button>
                </div>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-members-refresh"><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
            </div>
          </div>
