<script type="text/javascript">
  jaxon.dom.ready(function() {
    // Check if it is a mobile device
    if('ontouchstart' in document.documentElement)
    {
      // Lock screen in landscape mode
      // screen.orientation.lock('landscape');
      jaxon.ajax.message.info("{{ __('tontine.messages.screen.orientation') }}", "{{ __('common.titles.message') }}");
    }
  });
</script>
