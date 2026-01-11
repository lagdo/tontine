      <div class="portlet-body form">
        <form class="form-horizontal" role="form">
          <div class="module-body">
            <div class="form-group row">
              {!! $html->label(__('tontine.labels.tontine'),
                'guild_id')->class('col-sm-3 col-form-label') !!}
              <div class="col-sm-8">
                {!! $html->select('guild_id', $guilds, $current)
                  ->class('form-control')->id('guild_id') !!}
              </div>
            </div>
        </form>
      </div>
