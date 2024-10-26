@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $searchValue = Jaxon\jq('#txt-fee-member-search')->val();
  $rqTarget = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\Target::class);
  $rqTargetPage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\TargetPage::class);
  $rqCharge = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\LibreFee::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ $charge->name }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
@if ($charge->is_active)
@if (!$target)
                        <button type="button" class="btn btn-primary" @jxnClick($rqTarget->add())><i class="fa fa-plus"></i></button>
@else
                        <button type="button" class="btn btn-primary" @jxnClick($rqTarget->edit())><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqTarget->remove()
                          ->confirm(__('meeting.target.questions.remove')))><i class="fa fa-trash"></i></button>
@endif
@endif
                      </div>
                    </div>
                  </div>
@if (($target))
                  <div class="row">
                    <div class="col">
                      {{ __('meeting.target.titles.summary', [
                          'deadline' => $target->deadline->title,
                      ]) }}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="input-group">
                        {!! $htmlBuilder->text('search', '')->class('form-control')->id('txt-fee-member-search') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqTarget->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      {{ __('common.labels.amount') }}<br/>{{ $locale->formatMoney($target->amount, true) }}
                    </div>
                  </div>
@endif
                  <div @jxnShow($rqTargetPage)>
                  </div>
                  <nav @jxnPagination($rqTargetPage)>
                  </nav>
