@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('debtCalculator', 'Siak\Tontine\Service\Meeting\Credit\DebtCalculator')
@php
  $debt = $refund->debt;
  $amount = $debtCalculator->getDebtPayableAmount($debt, $session);
  $dueAmount = $locale->formatMoney($amount, true);
  $payableAmount = $locale->formatMoney($refund->amount + $amount, false);
  $refundAmount = $locale->getMoneyValue($refund->amount);
@endphp
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="refund-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label(__('meeting.labels.debt'), 'debt')->class('col-sm-2 col-form-label') !!}
              <div class="col-sm-10">
                {{ $debt->loan->member->name }}: {{ $payableAmount }}<br/> {{
                  __('meeting.loan.labels.' . $debt->type) }}: {{ $debt->loan->session->title }}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.amount'), 'amount')->class('col-sm-2 col-form-label') !!}
              <div class="col-sm-5">
                {!! $htmlBuilder->text('amount', $refundAmount)->class('form-control') !!}
              </div>
              <div class="col-sm-4">
                {!! $htmlBuilder->label("Max: $payableAmount", '')->class('col-form-label') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
