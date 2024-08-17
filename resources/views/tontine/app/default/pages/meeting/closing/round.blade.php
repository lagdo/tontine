@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="closing-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-sm-11">
                <p class="lead">{!! __('meeting.closing.labels.fund') !!}: {!! $fund->title !!}</p>
              </div>
            </div>
            <div class="form-group row">
              {!! $htmlBuilder->label(__('meeting.closing.labels.amount'), 'amount')->class('col-sm-5 col-form-label') !!}
              <div class="col-sm-6">
                {!! $htmlBuilder->text('amount', $closing !== null ?
                  $locale->convertMoneyToInt($closing->profit) : '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
