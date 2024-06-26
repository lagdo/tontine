@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="saving-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('member', __('meeting.labels.member'), ['class' => 'col-sm-3 col-form-label']) !!}
              <div class="col-sm-8">
                {!! Form::select('member', $members, $saving->member_id, ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('amount', __('common.labels.amount'), ['class' => 'col-sm-3 col-form-label']) !!}
              <div class="col-sm-5">
                {!! Form::text('amount', $locale->getMoneyValue($saving->amount), ['class' => 'form-control']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('fund', __('tontine.fund.labels.fund'), ['class' => 'col-sm-3 col-form-label']) !!}
              <div class="col-sm-8">
                {!! Form::select('fund', $funds, $saving->fund_id, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
