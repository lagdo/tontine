@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="funding-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('member', trans('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-8">
                {!! Form::hidden('member', $funding->member->id) !!}
                {!! Form::text('', $funding->member->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('amount', trans('common.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-sm-5">
                {!! Form::text('amount', $locale->getMoneyValue($funding->amount), ['class' => 'form-control']) !!}
              </div>
            </div>
            </div>
        </form>
      </div>
