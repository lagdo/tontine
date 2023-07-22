@inject('locale', 'Siak\Tontine\Service\LocaleService')
                        <div class="input-group">
                          {!! Form::text('amount', $locale->formatMoney($amount, true),
                            ['class' => 'form-control', 'readonly' => 'readonly', 'style' => 'text-align:right']) !!}
                          <div class="input-group-append" data-receivable-id="{{ $id }}">
                            <button type="button" class="btn btn-primary btn-edit-deposit"><i class="fa fa-edit"></i></button>
                          </div>
                        </div>
