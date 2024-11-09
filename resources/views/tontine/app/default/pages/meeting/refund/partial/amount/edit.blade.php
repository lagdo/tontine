@php
  $amountValue = Jaxon\pm()->input("partial-refund-amount-edit-{$debt->id}");
  $rqAmount = Jaxon\rq(App\Ajax\Web\Meeting\Session\Credit\Partial\Amount::class);
@endphp
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                            ->id("partial-refund-amount-edit-{$debt->id}")
                            ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqAmount
                              ->save($debt->id, $amountValue))><i class="fa fa-save"></i></button>
                          </div>
                        </div>
