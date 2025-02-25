@inject('locale', 'Siak\Tontine\Service\LocaleService')
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="target-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.amount'), 'amount')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-7">
                {!! $html->text('amount', $locale->getMoneyValue($target->amount))->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              <div class="offset-md-3 col-md-8">
                {!! $html->checkbox('global', $target->global, '1') !!}
                {!! $html->label(__('meeting.target.labels.global'), 'global')->class('form-check-label') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('meeting.target.labels.deadline'), 'deadline')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-9">
                {!! $html->select('deadline', $sessions, $target->deadline_id)->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
