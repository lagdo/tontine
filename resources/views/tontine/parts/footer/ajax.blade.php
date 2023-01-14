
{!! $jaxonJs !!}

{!! $jaxonScript !!}

{!! $jaxonCss !!}

<script type='text/javascript'>
  /* <![CDATA[ */
  jaxon.dom.ready(function() {
    $('#tontine-menu-members').click(function() { {!! $jxnMember->home() !!}; });
    $('#tontine-menu-charges').click(function() { {!! $jxnCharge->home() !!}; });
    $('#planning-menu-sessions').click(function() { {!! $jxnPlanning->home() !!}; });
    $('#planning-menu-pools').click(function() { {!! $jxnPool->home() !!}; });
    $('#planning-menu-reports').click(function() { {!! $jxnPlanningReport->home() !!}; });
    $('#meeting-menu-sessions').click(function() { {!! $jxnMeeting->home() !!}; });
    $('#meeting-menu-reports').click(function() { {!! $jxnMeetingReport->home() !!}; });
    $('#user-menu-profile').click(function() { {!! $jxnTontine->home() !!}; });
    // Tontine page
    $('#btn-tontine-refresh').click(function() { {!! $jxnTontine->home() !!}; });
    $('#btn-tontine-create').click(function() { {!! $jxnTontine->add() !!}; });
    $('.btn-tontine-edit').click(function() { {!! $jxnTontine->edit(Jaxon\jq()->parent()->attr('data-tontine-id')) !!}; });
    $('.btn-tontine-rounds').click(function() { {!! $jxnRound->home(Jaxon\jq()->parent()->attr('data-tontine-id')) !!}; });
@if(($tontine))
    {!! $jxnRound->home($tontine->id) !!};
@endif
  });
  /* ]]> */
</script>