      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="invite-form">
          <div class="module-body">
            <div class="form-group row">
              <div class="col-md-11">{!! __('tontine.invite.titles.add_desc') !!}</div>
            </div>
            <div class="form-group row">
              {!! $html->label(__('common.labels.email'), 'email')->class('col-sm-3 col-form-label') !!}
              <div class="col-md-8">
                {!! $html->text('email', '')->class('form-control') !!}
              </div>
            </div>
          </div>
        </form>
      </div>
