              <div class="section-body">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="section-title">{{ $session->title }} - {{ $member->name }}</h2>
                  </div>
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" id="btn-payment-members-back"><i class="fa fa-arrow-left"></i></button>
                  </div>
                  <div class="col-auto" id="payment-request">
                  </div>
                </div>
              </div>
@if($receivables->count() > 0)
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      @include('tontine::pages.meeting.payment.deposits', ['receivables' => $receivables])
                    </div>
                  </div>
                </div>
              </div>
@endif
@if($debts->count() > 0)
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      @include('tontine::pages.meeting.payment.debts', ['debts' => $debts])
                    </div>
                  </div>
                </div>
              </div>
@endif
@if($bills->count() > 0)
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      @include('tontine::pages.meeting.payment.bills', ['bills' => $bills])
                    </div>
                  </div>
                </div>
              </div>
@endif
