      <div class="portlet-body form">
        <form class="form-horizontal" role="form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('group', __('number.charge.type'), ['class' => 'col-md-8 col-form-label']) !!}
              <div class="col-md-4">
                {!! Form::select('group', $groups, 0, ['class' => 'form-control', 'id' => 'charge-group']) !!}
              </div>
            </div>
            <div class="form-group row">
                {!! Form::label('number', __('number.labels.charge'), ['class' => 'col-md-8 col-form-label']) !!}
                <div class="col-md-4">
                  {!! Form::text('number', '', ['class' => 'form-control', 'id' => 'text-number']) !!}
                </div>
              </div>
            </div>
        </form>
      </div>
