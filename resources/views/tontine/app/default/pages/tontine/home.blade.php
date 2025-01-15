@php
  $rqOrganisation = rq(Ajax\App\Admin\Organisation\Organisation::class);
  $rqOrganisationPage = rq(Ajax\App\Admin\Organisation\OrganisationPage::class);
  $rqGuestOrganisation = rq(Ajax\App\Admin\User\Guest\Organisation::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.titles.tontines') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2 mb-2" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqOrganisation->home())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqOrganisation->add())><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqOrganisationPage)>
            </div>
          </div>

@if ($hasGuestOrganisations)
          <div @jxnBind($rqGuestOrganisation)>
            @jxnHtml($rqGuestOrganisation)
          </div>
@endif
