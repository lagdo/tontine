      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="target-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('amount', __('common.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-7">
                {!! Form::text('amount', '', ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="offset-md-3 col-md-8">
                {!! Form::checkbox('global', '1', false) !!}
                {!! Form::label('global', __('meeting.target.labels.global'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('deadline', __('meeting.target.labels.deadline'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-9">
                {!! Form::select('deadline', $sessions, '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
