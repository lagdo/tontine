                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')->attribute('style', 'width:50px;border-color:#a1a1a1;') !!}
                          <div class="input-group-append" data-member-id="{{ $memberId }}">
                            <button type="button" class="btn btn-primary btn-save-saving"><i class="fa fa-save"></i></button>
                          </div>
                        </div>
