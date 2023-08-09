                <div class="btn-group float-right" role="group" aria-label="">
@if($session->pending || $session->closed)
                  <button type="button" class="btn btn-primary" id="btn-session-open"><i class="fa fa-lock"></i></button>
@elseif($session->opened)
                  <button type="button" class="btn btn-primary" id="btn-session-close"><i class="fa fa-lock-open"></i></button>
@endif
                </div>
