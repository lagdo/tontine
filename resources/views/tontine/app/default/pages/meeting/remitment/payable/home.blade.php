@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqRemitment = rq(Ajax\App\Meeting\Session\Pool\Remitment\Remitment::class);
  $rqPayableFunc = rq(Ajax\App\Meeting\Session\Pool\Remitment\PayableFunc::class);
  $rqPayablePage = rq(Ajax\App\Meeting\Session\Pool\Remitment\PayablePage::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">
                        {{ $pool->title }}<br/>{{ __('meeting.titles.remitments') }}@if (!$pool->remit_planned) ({{
                          $locale->formatMoney($depositAmount) }})@endif
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqRemitment->render())><i class="fa fa-arrow-left"></i></button>
@if (!$pool->remit_planned)
                        <button type="button" class="btn btn-primary" @jxnClick($rqPayableFunc->addRemitment(0))><i class="fa fa-plus"></i></button>
@endif
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqPayablePage)>
                  </div>
