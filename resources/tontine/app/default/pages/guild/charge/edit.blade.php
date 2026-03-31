      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="charge-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.type'), 'type')->class('col-sm-4 col-form-label') !!}
              <div class="col-md-5">
                {!! $html->label($types[$charge->type], 'type')->class('col-form-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.period'), 'period')->class('col-sm-4 col-form-label') !!}
              <div class="col-md-5">
                {!! $html->label($periods[$charge->period], 'period')->class('col-form-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.name'), 'name')->class('col-sm-4 col-form-label') !!}
              <div class="col-md-7">
                {!! $html->text('name', $charge->name)->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.amount') . " ($currency)", 'amount')
                ->class('col-sm-4 col-form-label') !!}
              <div class="col-md-6">
@if ($charge->is_fixed)
                {!! $html->text('amount', $charge->amount_value)->class('form-control') !!}
                {!! $html->hidden('fixed', '1') !!}
@else
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      {!! $html->checkbox('fixed', $charge->has_amount, '1') !!}
                    </div>
                  </div>
                  {!! $html->text('amount', $charge->has_amount ? $charge->amount_value : '')->class('form-control') !!}
                </div>
@endif
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('tontine.charge.labels.lendable'), 'lendable')->class('col-sm-4 col-form-label') !!}
              <div class="col-md-3 pt-2">
                {!! $html->checkbox('lendable', $charge->lendable, '1') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
