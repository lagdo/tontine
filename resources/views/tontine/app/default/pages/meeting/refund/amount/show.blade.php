@inject('paymentService', 'Siak\Tontine\Service\Meeting\PaymentServiceInterface')
@php
  $rqAmountFunc = rq(Ajax\App\Meeting\Session\Credit\Refund\AmountFunc::class);
@endphp
                        <div class="input-group">
                          {!! $html->text('amount', $amount)->attribute('readonly', 'readonly')
                            ->class('form-control')->attribute('style', 'height:36px; text-align:right') !!}
                          <div class="input-group-append">
@if($paymentService->isEditable($debt->partial_refund))
                            <button type="button" class="btn btn-primary" @jxnClick($rqAmountFunc->edit($debt->id))><i class="fa fa-edit"></i></button>
@else
                            <button type="button" class="btn btn-primary"><i class="fa fa-link"></i></button>
@endif
                          </div>
                        </div>
