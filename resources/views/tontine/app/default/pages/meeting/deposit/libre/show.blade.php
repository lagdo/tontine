                        <div class="input-group">
                          {!! $html->text('amount', $amount)
                            ->class('form-control')->attribute('readonly', 'readonly')
                            ->attribute('style', 'height:36px; text-align:right') !!}
@if ($editable)
                          <div class="input-group-append">
                            <button @jxnClick($rqAmountFunc->edit($receivableId)) type="button" class="btn btn-primary"><i class="fa fa-edit"></i></button>
                          </div>
@else    
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary"><i class="fa fa-link"></i></button>
                          </div>
@endif
                        </div>
