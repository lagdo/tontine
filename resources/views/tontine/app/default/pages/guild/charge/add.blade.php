          <div class="portlet-body form">
            <form class="form-horizontal" role="form" id="charge-form">
              <div class="module-body">
                <div class="form-group row">
@if ($fixed)
                  {!! $html->label(__('common.labels.period'), 'period')->class('col-sm-4 col-form-label') !!}
                  <div class="col-md-5">
                    {!! $html->select('period', $periods, '')->class('form-control') !!}
                  </div>
@else
                  {!! $html->label(__('common.labels.type'), 'type')->class('col-sm-4 col-form-label') !!}
                  <div class="col-md-5">
                    {!! $html->select('type', $types, '')->class('form-control') !!}
                  </div>
@endif
                </div>
                <div class="form-group row">
                  {!! $html->label(__('common.labels.name'), 'name')->class('col-sm-4 col-form-label') !!}
                  <div class="col-md-7">
                    {!! $html->text('name', '')->class('form-control') !!}
                  </div>
                </div>
                <div class="form-group row">
                  {!! $html->label(__('common.labels.amount') . " ($currency)", 'amount')
                    ->class('col-sm-4 col-form-label') !!}
                  <div class="col-md-6">
@if ($fixed)
                    {!! $html->text('amount', '')->class('form-control') !!}
                    {!! $html->hidden('fixed', '1') !!}
@else
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <div class="input-group-text">
                          {!! $html->checkbox('fixed', false, '1') !!}
                        </div>
                      </div>
                      {!! $html->text('amount', '')->class('form-control') !!}
                    </div>
@endif
                  </div>
                </div>
                <div class="form-group row">
                  {!! $html->label(__('tontine.charge.labels.lendable'), 'lendable')->class('col-sm-4 col-form-label') !!}
                  <div class="col-md-3 pt-2">
                    {!! $html->checkbox('lendable', false, '1') !!}
                  </div>
                </div>
              </div>
            </form>
          </div>
