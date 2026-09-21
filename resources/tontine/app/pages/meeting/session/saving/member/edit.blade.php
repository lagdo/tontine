@php
  $amountValue = jq("#saving-edit-member-$memberId")->val();
@endphp
                        <div class="input-group">
                          {!! $html->text('amount', $amount)->class('form-control')
                            ->id("saving-edit-member-$memberId")->attribute('style', 'height:36px;') !!}
                          <div class="input-group-append">
                            <button @jxnClick($rqAmountFunc->save($memberId, $amountValue)) type="button" class="btn btn-primary"><i class="fa fa-save"></i></button>
                          </div>
                        </div>
