@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="pool-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $htmlBuilder->label('', '')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-9">
                {!! $htmlBuilder->checkbox('', $pool->deposit_fixed, '1')->attribute('disabled', 'disabled') !!}
                {!! $htmlBuilder->label(__('tontine.pool.labels.deposit.fixed'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label('', '')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-9">
                {!! $htmlBuilder->checkbox('', $pool->deposit_lendable, '1')->attribute('disabled', 'disabled') !!}
                {!! $htmlBuilder->label(__('tontine.pool.labels.deposit.lendable'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label('', '')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-9">
                {!! $htmlBuilder->checkbox('', $pool->remit_planned, '1')->attribute('disabled', 'disabled') !!}
                {!! $htmlBuilder->label(__('tontine.pool.labels.remit.planned'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label('', '')->class('col-sm-2 col-form-label') !!}
              <div class="col-md-9">
                {!! $htmlBuilder->checkbox('', $pool->remit_auction, '1')->attribute('disabled', 'disabled') !!}
                {!! $htmlBuilder->label(__('tontine.pool.labels.remit.auction'), '')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.title'), 'title')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $htmlBuilder->text('title', $pool->title)->class('form-control') !!}
              </div>
            </div>
@if ($pool->deposit_fixed)
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.amount'), 'amount')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-6">
                {!! $htmlBuilder->text('amount', $locale->getMoneyValue($pool->amount))->class('form-control') !!}
              </div>
            </div>
@else
            {!! $htmlBuilder->hidden('amount', '0') !!}
@endif
            <div class="form-group row">
              {!! $htmlBuilder->label(__('common.labels.notes'), 'notes')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $htmlBuilder->textarea('notes', $pool->notes)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
