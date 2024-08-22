                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')
                            ->attribute('readonly', 'readonly')
                            ->attribute('style', 'height:36px; text-align:right;') !!}
                          <div class="input-group-append" data-member-id="{{ $memberId }}">
                            <button type="button" class="btn btn-primary btn-edit-saving"><i class="fa fa-edit"></i></button>
                          </div>
                        </div>
