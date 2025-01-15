@inject('locale', 'Siak\Tontine\Service\LocaleService')
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          {!! $htmlBuilder->span(__('meeting.loan.labels.amount_available', [
                              'amount' => $locale->formatMoney($amount),
                            ]))
                            ->class('input-group-text')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        </div>
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqBalance
                            ->details())><i class="fa fa-caret-right"></i></button>
                        </div>
                      </div>
