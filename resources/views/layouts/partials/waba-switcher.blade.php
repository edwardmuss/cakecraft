<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"> --}}
@if(isset($availableWabas) && $availableWabas->count())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <label class="form-label fw-bold text-muted small uppercase">Switch Business Account</label>
        <form id="waba-switcher-form" method="POST" action="{{ route('whatsapp.switch') }}">
            @csrf
            <select class="form-control select2 select2-waba" name="whatsapp_business_branch_id">
                @foreach($availableWabas as $waba)
                    <option value="{{ $waba->id }}" 
                        {{ $activeWaba?->id === $waba->id ? 'selected' : '' }}
                        data-id="{{ $waba->waba_id }}">
                        {{ $waba->business_name ?? $waba->waba_id }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>
@endif
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!--select2 cdn-->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('build/js/pages/select2.init.js') }}"></script>
<script>
    $(document).ready(function() {
        function formatWaba(state) {
            if (!state.id) return state.text;
            var wabaId = $(state.element).data('id');
            var $state = $(
                '<span><strong>' + state.text + '</strong><br/><small class="text-muted">' + wabaId + '</small></span>'
            );
            return $state;
        };

        // $('.select2-waba').select2({
        //     templateResult: formatWaba,
        //     theme: 'bootstrap-5', // Requires Select2 Bootstrap 5 theme
        //     placeholder: 'Select a WhatsApp Account'
        // });

        // Auto-submit form on change
        $('.select2-waba').on('change', function() {
            $('#waba-switcher-form').submit();
        });
    });
</script>