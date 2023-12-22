@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="closing-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('amount', __('meeting.profit.labels.amount'), ['class' => 'col-sm-5 col-form-label text-right']) !!}
              <div class="col-sm-6">
                {!! Form::text('amount', $hasClosing ? $locale->convertMoneyToInt($profitAmount) : '', ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
