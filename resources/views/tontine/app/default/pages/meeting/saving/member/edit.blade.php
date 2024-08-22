                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                            ->attribute('style', 'height:36px;') !!}
                          <div class="input-group-append" data-member-id="{{ $memberId }}">
                            <button type="button" class="btn btn-primary btn-save-saving"><i class="fa fa-save"></i></button>
                          </div>
                        </div>
