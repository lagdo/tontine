@php
  $amountValue = Jaxon\jq("#saving-edit-member-$memberId")->val();
@endphp
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                            ->id("saving-edit-member-$memberId")->attribute('style', 'height:36px;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($rqAmount
                              ->save($memberId, $amountValue))><i class="fa fa-save"></i></button>
                          </div>
                        </div>
