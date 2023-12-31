@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="pool-form">
          <div class="module-body">
            <div class="form-group row">
              {!! Form::label('', '', ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-md-9">
                {!! Form::checkbox('', '1', $pool->deposit_fixed, ['disabled' => 'disabled']) !!}
                {!! Form::label('', __('tontine.pool.labels.deposit.fixed'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('', '', ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-md-9">
                {!! Form::checkbox('', '1', $pool->deposit_lendable, ['disabled' => 'disabled']) !!}
                {!! Form::label('', __('tontine.pool.labels.deposit.lendable'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('', '', ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-md-9">
                {!! Form::checkbox('', '1', $pool->remit_planned, ['disabled' => 'disabled']) !!}
                {!! Form::label('', __('tontine.pool.labels.remit.planned'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('', '', ['class' => 'col-sm-2 col-form-label text-right']) !!}
              <div class="col-md-9">
                {!! Form::checkbox('', '1', $pool->remit_auction, ['disabled' => 'disabled']) !!}
                {!! Form::label('', __('tontine.pool.labels.remit.auction'), ['class' => 'form-check-label']) !!}
              </div>
            </div>
            <div class="form-group row">
              {!! Form::label('title', __('common.labels.title'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::text('title', $pool->title, ['class' => 'form-control']) !!}
              </div>
            </div>
@if ($pool->deposit_fixed)
            <div class="form-group row">
              {!! Form::label('amount', __('common.labels.amount'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-6">
                {!! Form::text('amount', $locale->getMoneyValue($pool->amount), ['class' => 'form-control']) !!}
              </div>
            </div>
@else
            {!! Form::hidden('amount', '0') !!}
@endif
            <div class="form-group row">
              {!! Form::label('notes', __('common.labels.notes'), ['class' => 'col-sm-3 col-form-label text-right']) !!}
              <div class="col-md-8">
                {!! Form::textarea('notes', $pool->notes, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
        </form>
      </div>
