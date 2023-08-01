@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="loan-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('member', trans('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::hidden('member', $loan->member->id) !!}
                {!! Form::text('', $loan->member->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('principal', trans('tontine.loan.labels.principal'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('principal', $locale->getMoneyValue($loan->principal), ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
                {!! Form::label('interest', trans('tontine.loan.labels.interest'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
                <div class="col-sm-5">
                  {!! Form::text('interest', $locale->getMoneyValue($loan->interest), ['class' => 'form-control']) !!}
                </div>
              </div>
            </div>
        </form>
      </div>
