@extends('layouts.master')
@section('title') SMS Templates @endsection 
@section('css')
    <link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Settings @endslot
    @slot('title') SMS Templates @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-bottom-dashed align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Automated SMS Notifications</h4>
                <div class="flex-shrink-0">
                    <p class="text-muted mb-0">Configure the messages sent to your customers.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 25%;">Trigger Event</th>
                                <th scope="col" style="width: 45%;">Message Template</th>
                                <th scope="col" style="width: 15%;">Status</th>
                                <th scope="col" style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $trigger => $data)
                            <tr>
                                <td>
                                    <h6 class="fs-15 mb-1">{{ $data['label'] }}</h6>
                                    <p class="text-muted mb-0"><small>{{ $data['description'] }}</small></p>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 350px;">
                                        @if($data['message'])
                                            <span class="text-muted">{{ $data['message'] }}</span>
                                        @else
                                            <span class="text-danger fst-italic">No template set. Using system default.</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($data['is_active'])
                                        <span class="badge bg-success-subtle text-success fs-12">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger fs-12">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-soft-primary" 
                                        onclick="editTemplate('{{ $trigger }}', '{{ $data['label'] }}', `{{ $data['message'] }}`, '{{ $data['placeholders'] }}', {{ $data['is_active'] ? 'true' : 'false' }})">
                                        <i class="ri-pencil-fill align-bottom me-1"></i> Configure
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="templateForm">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="modalTitle">Configure Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="trigger_name" name="trigger_name">
                    
                    <div class="mb-3">
                        <label class="form-label">Message Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Type your SMS message here..."></textarea>
                        <span class="text-danger error-text message_error"></span>
                    </div>

                    <div class="alert alert-info border-0 mb-4">
                        <strong>Available Placeholders:</strong><br>
                        <span id="placeholders_list" class="fs-12"></span><br>
                        <small class="fst-italic text-muted">Click a placeholder to insert it into your message.</small>
                    </div>

                    <div class="form-check form-switch form-switch-md mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active">
                        <label class="form-check-label" for="is_active">Enable this automated SMS</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('build/js/app.js') }}"></script>

<script>
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const templateModal = new bootstrap.Modal(document.getElementById('templateModal'));

    function editTemplate(trigger, label, message, placeholders, isActive) {
        document.getElementById('trigger_name').value = trigger;
        document.getElementById('modalTitle').innerText = 'Configure: ' + label;
        document.getElementById('message').value = message;
        document.getElementById('is_active').checked = isActive;
        
        // Format placeholders into clickable badges
        let phArray = placeholders.split(', ');
        let phHtml = '';
        phArray.forEach(ph => {
            phHtml += `<span class="badge bg-secondary-subtle text-secondary me-1 mb-1 cursor-pointer" onclick="insertPlaceholder('${ph}')" style="cursor: pointer;">${ph}</span>`;
        });
        document.getElementById('placeholders_list').innerHTML = phHtml;

        $('.error-text').text('');
        templateModal.show();
    }

    // Helper function to insert placeholder where the cursor is
    function insertPlaceholder(placeholder) {
        const textarea = document.getElementById('message');
        const startPos = textarea.selectionStart;
        const endPos = textarea.selectionEnd;
        
        textarea.value = textarea.value.substring(0, startPos) + placeholder + textarea.value.substring(endPos, textarea.value.length);
        textarea.focus();
        textarea.selectionStart = startPos + placeholder.length;
        textarea.selectionEnd = startPos + placeholder.length;
    }

    $('#templateForm').submit(function(e) {
        e.preventDefault();
        $('#saveBtn').prop('disabled', true).text('Saving...');
        $('.error-text').text(''); 

        let formData = {
            trigger_name: $('#trigger_name').val(),
            message: $('#message').val(),
            is_active: $('#is_active').is(':checked')
        };

        axios.post('/sms-templates', formData)
            .then(response => {
                templateModal.hide();
                Swal.fire('Success', response.data.success, 'success').then(() => location.reload());
            })
            .catch(error => {
                if(error.response && error.response.status === 422) {
                    let errors = error.response.data.errors;
                    for (const key in errors) {
                        $('.' + key + '_error').text(errors[key][0]);
                    }
                } else {
                    Swal.fire('Error', 'Something went wrong!', 'error');
                }
            })
            .finally(() => {
                $('#saveBtn').prop('disabled', false).text('Save Template');
            });
    });
</script>
@endsection