                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')->attribute('readonly', 'readonly')
                            ->attribute('style', 'height:36px; text-align:right') !!}
@if ($editable)
                          <div class="input-group-append" data-receivable-id="{{ $id }}">
                            <button type="button" class="btn btn-primary btn-edit-deposit"><i class="fa fa-edit"></i></button>
                          </div>
@else    
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary"><i class="fa fa-link"></i></button>
                          </div>
@endif
                        </div>
