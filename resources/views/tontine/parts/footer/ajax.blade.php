
{!! $jaxonJs !!}

{!! $jaxonScript !!}

{!! $jaxonCss !!}

<script type='text/javascript'>
  /* <![CDATA[ */
  jaxon.dom.ready(function() {
    $('#tontine-menu-tontines').click(function() { {!! $jxnTontine->home() !!}; });
    {!! $jxnTontine->home() !!};
  });
  /* ]]> */
</script>
