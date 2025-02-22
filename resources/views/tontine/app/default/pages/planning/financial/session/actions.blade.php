@php
  $formValues = pm()->form('pool-session-form');
  $rqSession = rq(Ajax\App\Planning\Financial\Session::class);
  $rqSessionFunc = rq(Ajax\App\Planning\Financial\SessionFunc::class);
@endphp
                      <div class="btn-group float-right ml-2 mb-2" role="group">
@if ($pool->pool_round)
                        <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->delete()
                          ->confirm(__('tontine.pool_round.questions.delete')))><i class="fa fa-times-circle"></i></button>
@endif
                        <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->save($formValues))><i class="fa fa-save"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqSession->render())><i class="fa fa-sync"></i></button>
                      </div>
