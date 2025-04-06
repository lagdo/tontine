@php
  $rqMember = rq(Ajax\App\Guild\Member\Member::class);
  $rqMemberFunc = rq(Ajax\App\Guild\Member\MemberFunc::class);
  $rqMemberPage = rq(Ajax\App\Guild\Member\MemberPage::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.menus.members') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqMember->home())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqMemberFunc->add())><i class="fa fa-plus"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqMemberFunc->addList())><i class="fa fa-list"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-header">
              <div class="row w-100">
                <div class="col-md-4 col-sd-12">
                  <div class="input-group">
                    {!! $html->text('search', '')->id('txt-member-search')->class('form-control')
                      ->attribute('style', 'height:36px; padding:5px;') !!}
                    <div class="input-group-append">
                      <button type="button" class="btn btn-primary" @jxnClick($rqMemberFunc
                        ->search(jq('#txt-member-search')->val()))><i class="fa fa-search"></i></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body" @jxnBind($rqMemberPage)>
            </div>
          </div>
