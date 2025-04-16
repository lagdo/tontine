      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="round-form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('common.labels.title'), 'title')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->text('title', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.notes'), 'notes')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->text('notes', '')->class('form-control') !!}
              </div>
            </div>
            <div class="form-group row">
              {!! $html->label('&nbsp;')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->checkbox('savings', false, '1') !!}
                {!! $html->label(__('tontine.round.labels.savings'), 'savings')->class('form-check-label') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
