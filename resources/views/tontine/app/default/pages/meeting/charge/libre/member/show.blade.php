                        <div class="input-group">
                          {!! $htmlBuilder->text('amount', $amount)->class('form-control')->attribute('readonly', 'readonly')->attribute('style', 'text-align:right') !!}
                          <div class="input-group-append" data-member-id="{{ $id }}">
                            <button type="button" class="btn btn-primary btn-edit-bill"><i class="fa fa-edit"></i></button>
                          </div>
                        </div>
