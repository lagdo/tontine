@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqPool = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Remitment\Pool::class);
  $rqPoolPage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Remitment\PoolPage::class);
  $rqRemitment = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Remitment::class);
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
                        <button type="button" class="btn btn-primary" @jxnClick($rqPool->addRemitment(0))><i class="fa fa-plus"></i></button>
@endif
                      </div>
                    </div>
                  </div>
                  <div @jxnShow($rqPoolPage)>
                  </div>
