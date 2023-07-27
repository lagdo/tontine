@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="row align-items-center">
            <div class="col-auto">
              <h6 class="section-title mt-0">{{ __('meeting.titles.refunds') }}</h6>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>{!! __('meeting.labels.member') !!}</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                  <th>{!! __('common.labels.amount') !!}</th>
                </tr>
              </thead>
              <tbody>
@foreach($loans as $loan)
                <tr>
                  <td>{{ $loan->member->name }}</td>
                  <td>{{ $loan->session->date }}</td>
                  <td>&nbsp;</td>
                  <td>{{ $loan->amount }}</td>
                </tr>
@endforeach
                <tr>
                  <th colspan="3">{!! __('common.labels.total') !!}</th>
                  <th>{{ $refundSum }}</th>
                </tr>
              </tbody>
            </table>
          </div> <!-- End table -->
