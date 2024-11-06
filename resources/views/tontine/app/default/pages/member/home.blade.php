@php
  $rqMember = Jaxon\rq(App\Ajax\Web\Tontine\Member\Member::class);
  $rqMemberPage = Jaxon\rq(App\Ajax\Web\Tontine\Member\MemberPage::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.menus.members') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqMember->home())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqMember->add())><i class="fa fa-plus"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqMember->addList())><i class="fa fa-list"></i></button>
                </div>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col">&nbsp;</div>
              <div class="col-auto">
                <div class="input-group">
                  {!! $htmlBuilder->text('search', '')->id('txt-member-search')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqMember->search(Jaxon\jq('#txt-member-search')->val()))><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" @jxnShow($rqMemberPage)>
            </div>
          </div>
