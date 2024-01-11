@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $settlementCount = $settlement->total ?? 0;
  $settlementAmount = $settlement->amount ?? 0;
@endphp
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>
@if ($type === 'fixed')
                            <div class="input-group">
                              {!! Form::text('search', $search, ['class' => 'form-control', 'id' => 'txt-fee-settlements-search']) !!}
                              <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="btn-fee-fixed-settlements-search"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
@else
                            {!! __('common.labels.name') !!}
@endif
                          </th>
                          <th class="currency">@if ($settlementCount > 0) {{ $settlementCount }}<br />{!!
                            $locale->formatMoney($settlementAmount, true) !!}@else &nbsp;@endif</th>
                          <th class="table-item-menu">
@if ($charge->is_variable)
                            {!! __('common.labels.paid') !!}
@elseif ($settlementCount < $billCount)
                            <a href="javascript:void(0)" class="btn-add-all-settlements"><i class="fa fa-toggle-off"></i></a>
@else
                            <a href="javascript:void(0)" class="btn-del-all-settlements"><i class="fa fa-toggle-on"></i></a>
@endif
                          </th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($bills as $bill)
                        <tr>
                          <td>{{ $bill->member->name }}@if ($bill->session) <br/>{{ $bill->session->title }} @endif</td>
                          <td class="currency">{{ $locale->formatMoney($bill->amount, true) }}</td>
                          <td class="table-item-menu" data-bill-id="{{ $bill->id }}">
                            {!! paymentLink($bill->settlement, 'settlement', !$session->opened) !!}
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
