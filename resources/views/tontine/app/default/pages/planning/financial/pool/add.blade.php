      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="pool-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-md-11">
                {!! $htmlBuilder->checkbox('', $properties['deposit']['fixed'], '1')->attribute('disabled', 'disabled') !!}
                {!! $htmlBuilder->label(__('tontine.pool.labels.deposit.fixed'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! $htmlBuilder->checkbox('', $properties['deposit']['lendable'], '1')->attribute('disabled', 'disabled') !!}
                {!! $htmlBuilder->label(__('tontine.pool.labels.deposit.lendable'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! $htmlBuilder->checkbox('', $properties['remit']['planned'], '1')->attribute('disabled', 'disabled') !!}
                {!! $htmlBuilder->label(__('tontine.pool.labels.remit.planned'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-11">
                {!! $htmlBuilder->checkbox('', $properties['remit']['auction'], '1')->attribute('disabled', 'disabled') !!}
                {!! $htmlBuilder->label(__('tontine.pool.labels.remit.auction'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.title'), 'title')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-9">
                {!! $htmlBuilder->text('title', '')->class('form-control') !!}
              </div>
            </div>
@if ($properties['deposit']['fixed'])
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.amount'), 'amount')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-6">
                {!! $htmlBuilder->text('amount', '')->class('form-control') !!}
              </div>
            </div>
@else
            {!! $htmlBuilder->hidden('amount', '0') !!}
@endif
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.notes'), 'notes')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-9">
                {!! $htmlBuilder->textarea('notes', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
