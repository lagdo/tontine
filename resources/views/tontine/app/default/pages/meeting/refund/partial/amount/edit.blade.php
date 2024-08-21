                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                             ->attribute('style', 'height:36px; width:50px; border-color:#a1a1a1;') !!}
                          <div class="input-group-append" data-debt-id="{{ $debt->id }}">
                            <button type="button" class="btn btn-primary btn-partial-refund-save-amount"><i class="fa fa-save"></i></button>
                          </div>
                        </div>
