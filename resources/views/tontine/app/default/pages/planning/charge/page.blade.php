@php
  $defId = jq()->parent()->attr('data-def-id')->toInt();
  $rqChargeFunc = rq(Ajax\App\Planning\Charge\ChargeFunc::class);
  $rqChargePage = rq(Ajax\App\Planning\Charge\ChargePage::class);
@endphp
                  <div class="table-responsive" id="content-charge-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-charge-enable', 'click'], $rqChargeFunc->enable($defId))></div>
                    <div @jxnEvent(['.btn-charge-disable', 'click'], $rqChargeFunc->disable($defId)
                      ->confirm(__('tontine.charge.questions.disable')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="table-menu">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($defs as $def)
@php
  $count = $def->charges->count();
  $toggleClass = $count > 0 ? 'btn-charge-disable' : 'btn-charge-enable';
  $toggleIcon = $count > 0 ? 'fa fa-toggle-on' : 'fa fa-toggle-off';
@endphp
                        <tr>
                          <td>{!! $def->name !!}</td>
                          <td class="table-item-toggle" data-def-id="{{ $def->id }}">
                            <a role="link" tabindex="0" class="{{ $toggleClass }}"><i class="{{ $toggleIcon }}"></i></a>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqChargePage)>
                    </nav>
                  </div> <!-- End table -->
