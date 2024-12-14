@php
  $formValues = pm()->form('end-session-form');
  $rqEndSession = rq(Ajax\App\Planning\Pool\Session\Pool\EndSession::class);
  $rqEndSessionPage = rq(Ajax\App\Planning\Pool\Session\Pool\EndSessionPage::class);
@endphp
                      <div class="btn-group float-right ml-2 mb-2" role="group">
@if ($pool->pool_round)
                        <button type="button" class="btn btn-primary" @jxnClick($rqEndSessionPage->current())><i class="fa fa-arrow-circle-down"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqEndSession->delete()
                          ->confirm(__('tontine.pool_round.questions.delete')))><i class="fa fa-times-circle"></i></button>
@endif
                        <button type="button" class="btn btn-primary" @jxnClick($rqEndSession->save($formValues))><i class="fa fa-save"></i></button>
                      </div>
