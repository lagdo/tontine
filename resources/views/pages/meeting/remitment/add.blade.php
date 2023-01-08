      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="remitment-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('subscription', trans('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('subscription', $members, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('interest', trans('tontine.loan.labels.interest'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('interest', '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
