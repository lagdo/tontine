      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="funding-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('member', trans('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('member', $members, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('amount', trans('common.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('amount', '', ['class' => 'form-control']) !!}
              </div>
            </div>
            </div>
        </form>
      </div>