@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="loan-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('member', __('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('member', $members, $loan->member->id, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('principal', __('meeting.loan.labels.principal'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('principal', $locale->getMoneyValue($loan->principal), ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('fund_id', __('tontine.fund.labels.fund'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::select('fund_id', $funds, $loan->fund_id, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">{{ __('meeting.loan.labels.interest') }}</div>
            </div>
            <div class="form-group row">
              {!! Form::label('interest_type', __('common.labels.type'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-7">
                {!! Form::select('interest_type', $interestTypes, $loan->interest_type, ['class' => 'form-control', 'id' => 'loan-interest-type']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('interest', $loan->fixed_interest ? __('common.labels.amount') : __('meeting.loan.labels.percentage'),
                ['class' => 'col-sm-3 col-form-label text-right', 'id' => 'loan-interest-label']) !!}
              <div class="col-sm-5">
                {!! Form::text('interest', $loan->fixed_interest ? $locale->getMoneyValue($loan->interest) :
                  $loan->interest_rate / 100, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
