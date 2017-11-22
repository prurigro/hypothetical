<script type="text/javascript">
    var env = {
        appName: "{{ env('APP_NAME') }}",
        appDesc: "{{ env('APP_DESC') }}",
        apiToken: "{{ Auth::check() ? '?api_token=' . Auth::user()->api_token : '' }}",
        csrfToken: "{{ csrf_token() }}",
        debug: {{ Config::get('app.debug') ? 'true' : 'false' }}
    };
</script>
