@php
  $amountAvailable = $locale->getMoneyValue($amountAvailable > 0 ? $amountAvailable : 0);
@endphp
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="loan-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('meeting.labels.member'), 'member')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('member', $members, 0)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('meeting.loan.labels.principal'), 'principal')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-5">
                {!! $html->text('principal', $amountAvailable)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('meeting.loan.labels.fund'), 'fund')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('fund', $funds, '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-12">{{ __('meeting.loan.labels.interest') }}</div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.type'), 'interest_type')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-7">
                {!! $html->select('interest_type', $interestTypes, 'f')->class('form-control')->id('loan-interest-type') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.amount'), 'interest')->class('col-sm-3 col-form-label')->id('loan-interest-label') !!}
              <div class="col-sm-5">
                {!! $html->text('interest', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
