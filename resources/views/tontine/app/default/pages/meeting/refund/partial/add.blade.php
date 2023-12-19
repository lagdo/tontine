      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="refund-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('debt', __('meeting.labels.debt'), ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-sm-10">
                {!! Form::select('debt', $debts, 0, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('amount', __('common.labels.amount'), ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('amount', '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
