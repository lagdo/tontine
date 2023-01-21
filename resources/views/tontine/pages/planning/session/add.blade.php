          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-sm-8">
                <h2 class="section-title">{{ __('tontine.session.titles.add') }}</h2>
              </div>
              <div class="col-sm-4">
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-cancel"><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-copy"><i class="fa fa-copy"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-save"><i class="fa fa-save"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="portlet-body form">
                <form class="form-horizontal" role="form" id="session-form">
                  <div class="module-body">
                    <div class="form-group row">
                      {!! Form::label('title', trans('common.labels.title'), ['class' => 'col-sm-5 col-form-label']) !!}
                      {!! Form::label('date', trans('common.labels.date'), ['class' => 'col-sm-3 col-form-label']) !!}
                      {!! Form::label('start', trans('common.labels.start'), ['class' => 'col-sm-2 col-form-label']) !!}
                      {!! Form::label('end', trans('common.labels.end'), ['class' => 'col-sm-2 col-form-label']) !!}
                    </div>
@for($i = 0; $i < $count; $i++)
                    <div class="form-group row">
                      <div class="col-sm-5">
                        {!! Form::text('sessions[' . $i . '][title]', '', ['class' => 'form-control', 'id' => "session_title_$i"]) !!}
                      </div>
                      <div class="col-sm-3">
                        {!! Form::date('sessions[' . $i . '][date]', '', ['class' => 'form-control', 'id' => "session_date_$i"]) !!}
                      </div>
                      <div class="col-sm-2">
                        {!! Form::time('sessions[' . $i . '][start]', '', ['class' => 'form-control', 'id' => "session_start_$i"]) !!}
                      </div>
                      <div class="col-sm-2">
                        {!! Form::time('sessions[' . $i . '][end]', '', ['class' => 'form-control', 'id' => "session_end_$i"]) !!}
                      </div>
                    </div>
@endfor
                  </div>
                </form>
              </div>
            </div>
          </div>
