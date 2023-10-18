                        <div class="input-group">
                          {!! Form::text('amount', $amount,
                            ['class' => 'form-control', 'readonly' => 'readonly', 'style' => 'text-align:right']) !!}
                          <div class="input-group-append" data-member-id="{{ $id }}">
                            <button type="button" class="btn btn-primary btn-edit-bill"><i class="fa fa-edit"></i></button>
                          </div>
                        </div>
