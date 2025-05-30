@php
  // $rqPayment = rq(Ajax\App\Meeting\Payment\Payment::class);
  $rqPaymentPage = rq(Ajax\App\Meeting\Payment\PaymentPage::class);
  $rqPayable = rq(Ajax\App\Meeting\Payment\Payable::class);
@endphp
          <div class="row" id="payment-sm-screens">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="payment-members-home">
              <div class="section-body" id="payment-section-home">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">{{ __('tontine.menus.payments') }}</h2>
                  </div>
@if ($sessions->count() > 0)
                  <div class="col-auto ml-auto">
                    {{ $html->select('session_id', $sessions, 0)->class('form-control')->id('select-session') }}
                  </div>
@endif
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('tontine.menus.members') }}</div>
                    </div>
                  </div>
                  <div @jxnBind($rqPaymentPage)>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="payment-payables-home" @jxnBind($rqPayable)>
            </div>
          </div>
