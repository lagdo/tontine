                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          {!! $html->span(__('meeting.outflow.labels.amount_available', [
                              'amount' => $locale->formatMoney($amount),
                            ]))
                            ->class('input-group-text')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        </div>
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqBalance
                            ->details())><i class="fa fa-caret-right"></i></button>
                          <button type="button" class="btn btn-primary" @jxnClick($rqBalance
                            ->render())><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
