@php
  $formValues = pm()->form('start-session-form');
  $rqStartSession = rq(Ajax\App\Planning\Pool\Session\StartSession::class);
  $rqStartSessionPage = rq(Ajax\App\Planning\Pool\Session\StartSessionPage::class);
@endphp
                      <div class="btn-group float-right ml-2 mb-2" role="group">
@if ($pool->pool_round)
                        <button type="button" class="btn btn-primary" @jxnClick($rqStartSessionPage->current())><i class="fa fa-arrow-circle-down"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqStartSession->delete()
                          ->confirm(__('tontine.pool_round.questions.delete')))><i class="fa fa-times-circle"></i></button>
@endif
                        <button type="button" class="btn btn-primary" @jxnClick($rqStartSession->save($formValues))><i class="fa fa-save"></i></button>
                      </div>
