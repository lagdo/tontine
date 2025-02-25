                        <div class="input-group">
                          {!! $html->text('amount', $amount)
                            ->id("member-charge-input-$memberId")->class('form-control')
                            ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" @jxnClick($handler)><i class="fa fa-save"></i></button>
                          </div>
                        </div>
