@php
  $rqPool = Jaxon\rq(App\Ajax\Web\Planning\Pool\Pool::class);
  $roundFormValues = Jaxon\pm()->form('round-form');
  $rqPoolRound = Jaxon\rq(App\Ajax\Web\Planning\Pool\PoolRound::class);
@endphp
                <div class="btn-group float-right ml-2 mb-2" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->home())><i class="fa fa-arrow-left"></i></button>
@if ($pool->pool_round)
                  <button type="button" class="btn btn-primary" @jxnClick($rqPoolRound->deleteRound()
                    ->confirm(__('tontine.pool_round.questions.delete')))><i class="fa fa-times-circle"></i></button>
@endif
                  <button type="button" class="btn btn-primary" @jxnClick($rqPoolRound->saveRound($roundFormValues))><i class="fa fa-save"></i></button>
                </div>
