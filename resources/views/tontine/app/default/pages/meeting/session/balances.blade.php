@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $in = ['auctions', 'charges', 'deposits', 'savings', 'refunds'];
  $out = ['remitments', 'disbursements', 'loans'];
  $total = array_reduce(Arr::only($balances, $in), fn($item, $sum) => $item + $sum, 0)
    - array_reduce(Arr::only($balances, $out), fn($item, $sum) => $item + $sum, 0);
@endphp
      <div class="portlet-body form">
        <form class="form-horizontal" role="form" id="session-form">
          <div class="module-body">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th></th>
                    <th>{!! $locale->formatMoney($total) !!}</th>
                  </tr>
                </thead>
                <tbody>
@foreach ($balances as $name => $amount)
                  <tr>
                    <td>{!! __("meeting.titles.$name") !!}</td>
                    <td>{!! $locale->formatMoney($amount) !!}</td>
                  </tr>
@endforeach
                </tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
