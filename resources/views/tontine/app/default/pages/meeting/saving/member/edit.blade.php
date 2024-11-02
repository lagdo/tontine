@php
  $amountValue = Jaxon\jq("#saving-edit-member-$memberId")->val();
  $rqMember = Jaxon\rq(App\Ajax\Web\Meeting\Session\Saving\Member::class);
@endphp
                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                            ->id("saving-edit-member-$memberId")->attribute('style', 'height:36px;') !!}
                          <div class="input-group-append" data-member-id="{{ $memberId }}">
                            <button type="button" class="btn btn-primary" @jxnClick($rqMember
                              ->saveSaving($memberId, $amountValue))><i class="fa fa-save"></i></button>
                          </div>
                        </div>
