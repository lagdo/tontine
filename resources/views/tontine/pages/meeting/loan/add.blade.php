      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="loan-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('member', __('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
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
              {!! Form::label('fund_id', __('tontine.fund.labels.fund'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('fund_id', $funds, '', ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">{{ __('meeting.loan.labels.interest') }}</div>
            </div>
            <div class="form-group row">
              {!! Form::label('interest_type', __('common.labels.type'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-7">
                {!! Form::select('interest_type', $interestTypes, 'f', ['class' => 'form-control', 'id' => 'loan-interest-type']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('interest', __('common.labels.amount'),
                ['class' => 'col-sm-3 col-form-label text-right', 'id' => 'loan-interest-label']) !!}
              <div class="col-sm-5">
                {!! Form::text('interest', '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
