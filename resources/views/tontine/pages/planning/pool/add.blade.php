          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-sm-8">
                <h2 class="section-title">{{ __('tontine.pool.titles.add') }}</h2>
              </div>
              <div class="col-sm-4">
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-cancel"><i class="fa fa-arrow-left"></i></button>
@if($useFaker)
                  <button type="button" class="btn btn-primary" id="btn-fakes"><i class="fa fa-fill"></i></button>
@endif
                  <button type="button" class="btn btn-primary" id="btn-save"><i class="fa fa-save"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="portlet-body form">
                <form class="form-horizontal" role="form" id="pool-form">
                  <div class="module-body">
                    <div class="form-group row">
                      {!! Form::label('title', __('common.labels.title'), ['class' => 'col-sm-4 col-form-label']) !!}
                      {!! Form::label('amount', __('common.labels.amount'), ['class' => 'col-sm-2 col-form-label']) !!}
                      {!! Form::label('notes', __('common.labels.notes'), ['class' => 'col-sm-6 col-form-label']) !!}
                    </div>
@for($i = 0; $i < $count; $i++)
                    <div class="form-group row">
                      <div class="col-sm-4">
                        {!! Form::text('pools[' . $i . '][title]', '', ['class' => 'form-control', 'id' => "pool_title_$i"]) !!}
                      </div>
                      <div class="col-sm-2">
                        {!! Form::text('pools[' . $i . '][amount]', '', ['class' => 'form-control', 'id' => "pool_amount_$i"]) !!}
                      </div>
                      <div class="col-sm-6">
                        {!! Form::text('pools[' . $i . '][notes]', '', ['class' => 'form-control', 'id' => "pool_notes_$i"]) !!}
                      </div>
                    </div>
@endfor
                  </div>
                </form>
              </div>
            </div>
          </div>
