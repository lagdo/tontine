                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)
                            ->class('form-control')->attribute('readonly', 'readonly')
                            ->attribute('style', 'height:36px; text-align:right') !!}
                          <div class="input-group-append">
                            <button @jxnClick($rqAmountFunc->edit($memberId)) type="button" class="btn btn-primary"><i class="fa fa-edit"></i></button>
                          </div>
                        </div>
