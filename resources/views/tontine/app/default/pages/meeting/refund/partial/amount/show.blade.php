@inject('paymentService', 'Siak\Tontine\Service\Meeting\PaymentServiceInterface')
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                            ->attribute('readonly', 'readonly')->attribute('style', 'height:36px; text-align:right') !!}
                          <div class="input-group-append" data-debt-id="{{ $debt->id }}">
@if($paymentService->isEditable($debt->partial_refund))
                            <button type="button" class="btn btn-primary btn-partial-refund-edit-amount"><i class="fa fa-edit"></i></button>
@else
                            <button type="button" class="btn btn-primary"><i class="fa fa-link"></i></button>
@endif
                          </div>
                        </div>
