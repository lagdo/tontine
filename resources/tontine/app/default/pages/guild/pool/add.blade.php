      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="pool-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-md-11">
                {!! $html->checkbox('', $properties['deposit']['fixed'], '1')->attribute('disabled', 'disabled') !!}
                {!! $html->label(__('tontine.pool.labels.deposit.fixed'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! $html->checkbox('', $properties['deposit']['lendable'], '1')->attribute('disabled', 'disabled') !!}
                {!! $html->label(__('tontine.pool.labels.deposit.lendable'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! $html->checkbox('', $properties['remit']['planned'], '1')->attribute('disabled', 'disabled') !!}
                {!! $html->label(__('tontine.pool.labels.remit.planned'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! $html->checkbox('', $properties['remit']['auction'], '1')->attribute('disabled', 'disabled') !!}
                {!! $html->label(__('tontine.pool.labels.remit.auction'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.title'), 'title')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-9">
                {!! $html->text('title', '')->class('form-control') !!}
              </div>
            </div>
@if ($properties['deposit']['fixed'])
            <div class="form-group row">
              {!! $html->label(__('common.labels.amount'), 'amount')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-6">
                {!! $html->text('amount', '')->class('form-control') !!}
              </div>
            </div>
@else
            {!! $html->hidden('amount', '0') !!}
@endif
            <div class="form-group row">
              {!! $html->label(__('common.labels.notes'), 'notes')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-9">
                {!! $html->textarea('notes', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
