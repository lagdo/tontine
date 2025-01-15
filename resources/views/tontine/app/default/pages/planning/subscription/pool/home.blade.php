          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.pool.titles.subscriptions') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqPoolPage)>
            </div>
          </div>
