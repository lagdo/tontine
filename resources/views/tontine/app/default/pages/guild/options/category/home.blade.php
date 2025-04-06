@php
  $rqCategory = rq(Ajax\App\Guild\Options\Category::class);
  $rqCategoryFunc = rq(Ajax\App\Guild\Options\CategoryFunc::class);
  $rqCategoryPage = rq(Ajax\App\Guild\Options\CategoryPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.category.titles.categories') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqCategory->render())><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqCategoryFunc->add())><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body" @jxnBind($rqCategoryPage)>
                </div>
              </div>
