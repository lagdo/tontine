
{!! $jaxonJs !!}

{!! $jaxonScript !!}

{!! $jaxonCss !!}

<script type='text/javascript'>
  jaxon.dom.ready(function() {
    $('#tontine-menu-tontines').click(function() { {!! $jxnTontine->home() !!}; });
    {!! $jxnTontine->home() !!};
  });

  function setSessionExportLink()
  {
    const baseUrl = '{{ substr(route('report.session', ['sessionId' => 0]), 0, -1) }}';
    $('#btn-session-export').attr('href', baseUrl + $('#select-session').val());
  }

  function setLoanInterestLabel()
  {
    $('#loan-interest-type').change(() => {
        const type = $('#loan-interest-type').val();
        $('#loan-interest-label').html(type === 'f' ? "{{ __('common.labels.amount') }}" : "{{ __('meeting.loan.labels.percentage') }}");
    });
  }
</script>
