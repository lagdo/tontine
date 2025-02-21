      <div class="portlet-body form">
        <form role="form" id="session-list">
          <div class="module-body">
            <div class="form-group">
              <div>{!! __('tontine.session.tips.add') !!}</div>
              <div>{!! __('tontine.session.tips.example') !!}</div>
            </div>
            <div class="form-group">
              {!! $htmlBuilder->textarea('sessions', '')->class('form-control')
                ->id('new-sessions-list')->attribute('style', 'height:240px') !!}
            </div>
          </div>
        </form>
      </div>
