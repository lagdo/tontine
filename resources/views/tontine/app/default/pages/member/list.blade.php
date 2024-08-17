      <div class="portlet-body form">
        <form role="form" id="member-list">
          <div class="module-body">
            <div class="form-group">
              <div>{!! __('tontine.member.tips.add') !!}</div>
              <div>{!! __('tontine.member.tips.example') !!}</div>
            </div>
            <div class="form-group">
              {!! $htmlBuilder->textarea('members', '')->class('form-control')->id('new-members-list')->attribute('style', 'height:240px') !!}
            </div>
          </div>
        </form>
      </div>
