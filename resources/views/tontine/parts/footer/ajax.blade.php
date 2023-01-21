
{!! $jaxonJs !!}

{!! $jaxonScript !!}

{!! $jaxonCss !!}

<script type='text/javascript'>
  /* <![CDATA[ */
  jaxon.dom.ready(function() {
    $('#btn-show-select').click(function() { {!! $jxnSelect->show() !!}; });
    $('#tontine-menu-tontines').click(function() { {!! $jxnTontine->home() !!}; });
    {!! $jxnTontine->home() !!};
  });
  /* ]]> */
</script>
