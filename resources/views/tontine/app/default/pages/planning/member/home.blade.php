@php
  $rqMember = rq(Ajax\App\Planning\Member\Member::class);
  $rqMemberPage = rq(Ajax\App\Planning\Member\MemberPage::class);
  $rqMemberCount = rq(Ajax\App\Planning\Member\MemberCount::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ __('tontine.member.titles.members') }}</h2>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqMember->toggleFilter())><i class="fa fa-filter"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-header">
              <div class="row w-100">
                <div class="col">
                  <div class="input-group">
                    {!! $html->text('search', '')->id('txt-member-search')->class('form-control')
                      ->attribute('style', 'height:36px; padding:5px;') !!}
                    <div class="input-group-append">
                      <button type="button" class="btn btn-primary" @jxnClick($rqMember
                        ->search(jq('#txt-member-search')->val()))><i class="fa fa-search"></i></button>
                    </div>
                  </div>
                </div>
                <div class="col-auto ml-auto pt-1" @jxnBind($rqMemberCount)>
                   @jxnHtml($rqMemberCount)
                </div>
              </div>
            </div>
            <div class="card-body" @jxnBind($rqMemberPage)>
            </div>
          </div>
