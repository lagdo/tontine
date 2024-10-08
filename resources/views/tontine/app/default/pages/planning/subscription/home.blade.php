@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="row sm-screen-selector mb-3" id="pool-subscription-sm-screens">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="pool-subscription-members" type="button" class="btn btn-primary">
                  {{ __('tontine.pool.titles.subscriptions') }}
                </button>
                <button data-target="pool-subscription-sessions" type="button" class="btn btn-outline-primary">
                  {{ __('tontine.pool.titles.sessions') }}
                </button>
              </div>
            </div>
          </div>

          <div class="row" id="content-page">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="pool-subscription-members">
            </div>
            <div class="col-md-6 col-sm-12 sm-screen" id="pool-subscription-sessions">
            </div>
          </div>
