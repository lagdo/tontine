      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="loan-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('member', trans('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('member', $members, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('principal', __('meeting.loan.labels.principal'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('principal', $amountAvailable, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
                {!! Form::label('interest', __('meeting.loan.labels.interest'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
                <div class="col-sm-5">
                  {!! Form::text('interest', '', ['class' => 'form-control']) !!}
                </div>
              </div>
            </div>
        </form>
      </div>
