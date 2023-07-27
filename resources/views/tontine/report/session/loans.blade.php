@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="row align-items-center">
            <div class="col-auto">
              <h6 class="section-title mt-0">{{ __('meeting.titles.loans') }}</h6>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>{!! __('meeting.labels.member') !!}</th>
                  <th>{!! __('common.labels.amount') !!}</th>
                  <th>{!! __('common.labels.interest') !!}</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
@foreach ($loans as $loan)
                <tr>
                  <td>{{ $loan->title }}</td>
                  <td>{{ $loan->amount }}</td>
                  <td>{{ $loan->paid }}</td>
                  <td>{{ $loan->paid }}</td>
                </tr>
@endforeach
                <tr>
                  <th>{!! __('common.labels.total') !!}</th>
                  <th>&nbsp;</th>
                  <th>{{ $sum['paid'] }}</th>
                  <th>{{ $sum['paid'] }}</th>
                </tr>
              </tbody>
            </table>
          </div> <!-- End table -->
