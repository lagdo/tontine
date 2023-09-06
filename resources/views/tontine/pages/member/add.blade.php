          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-sm-8">
                <h2 class="section-title">{{ __('tontine.member.titles.add') }}</h2>
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
                <form class="form-horizontal" role="form" id="member-form">
                  <div class="module-body">
                    <div class="form-group row">
                      {!! Form::label('name', __('common.labels.name'), ['class' => 'col-sm-3 col-form-label']) !!}
                      {!! Form::label('email', __('common.labels.email'), ['class' => 'col-sm-4 col-form-label']) !!}
                      {!! Form::label('phone', __('common.labels.phone'), ['class' => 'col-sm-3 col-form-label']) !!}
                    </div>
@for($i = 0; $i < $count; $i++)
                    <div class="form-group row">
                      <div class="col-sm-3">
                        {!! Form::text('members[' . $i . '][name]', '', ['class' => 'form-control', 'id' => "member_name_$i"]) !!}
                      </div>
                      <div class="col-sm-4">
                        {!! Form::text('members[' . $i . '][email]', '', ['class' => 'form-control', 'id' => "member_email_$i"]) !!}
                      </div>
                      <div class="col-sm-3">
                        {!! Form::text('members[' . $i . '][phone]', '', ['class' => 'form-control', 'id' => "member_phone_$i"]) !!}
                      </div>
                    </div>
@endfor
                  </div>
                </form>
              </div>
            </div>
          </div>
