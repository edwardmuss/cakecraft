@extends('layouts.master')
@section('title') Communication @endsection
@section('css')
    <link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        :root {
            --bs-primary: #083b36;
            --bs-primary-hover: #062d29;
        }
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        .btn-primary:hover {
            background-color: var(--bs-primary-hover);
            border-color: var(--bs-primary-hover);
        }
        .merge-badge {
            cursor: pointer;
            margin-right: 6px;
            user-select: none;
            transition: all 0.2s;
        }
        .merge-badge:hover {
            background-color: #EB6D2F !important;
            transform: translateY(-1px);
        }
        .ck-editor__editable {
            min-height: 250px;
        }
        .action-container {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
        #previewBox {
            background-color: #f8f9fa;
            border-left: 4px solid var(--bs-primary);
            padding: 15px;
            white-space: pre-wrap;
        }
    </style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Communication @endslot
    @slot('title') SMS & Email @endslot
@endcomponent

<div class="container-fluid bg-white p-4 shadow-sm rounded">
    <div class="row">
        <div class="col-md-6">
            <label class="form-label fw-bold">Channel</label>
            <select id="channel" class="form-select">
                <option value="sms" selected>SMS</option>
                <option value="email">Email</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Status Filter</label>
            <select id="account_status" class="form-select">
                <option value="all">All</option>
                <option selected value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="dormant">Dormant</option>
            </select>
        </div>
    </div>

    <hr>

    <!-- Subject Row -->
    <div class="row mt-3 d-none" id="subjectRow">
        <div class="col-md-12">
            <label class="form-label fw-bold text-danger">Subject *</label>
            <input id="subject" type="text" class="form-control" placeholder="Enter email subject">
        </div>
    </div>

    <!-- Time to Send Row -->
    <div class="row mt-3" id="timeToSendRow">
        <div class="col-md-12">
            <label class="form-label fw-bold text-danger">Time to Send *</label>
            <input type="datetime-local" id="time_to_send" class="form-control">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <label class="form-label fw-bold">Message</label>
            <div class="mb-2" id="mergeTagBadges">
                <span class="badge bg-primary merge-badge" data-tag="#branch_name#">Company Name</span>
                <span class="badge bg-primary merge-badge" data-tag="#BALANCE#">Balance</span>
                <span class="badge bg-primary merge-badge" data-tag="#kra_pin_number#">Account Number</span>
            </div>
            <textarea id="message" class="form-control" rows="6" placeholder="Type your message here..."></textarea>
        </div>

        <div class="col-12">
            <div class="alert alert-warning d-none mt-3" id="recipientPreview">
                <strong>Recipients:</strong> <span id="recipientCount">0</span><br>
                <span id="smsUnitStats">
                    <strong>Estimated SMS Units:</strong> <span id="unitCount">0</span>
                </span>
            </div>
            
            <div class="mt-3 d-none" id="previewContainer">
                <label class="form-label fw-bold">Message Preview</label>
                <div id="previewBox" class="rounded border"></div>
            </div>
        </div>
    </div>

    <!-- Buttons Row -->
    <div class="row action-container">
        <div class="col-12 d-flex justify-content-end gap-2">
            <button class="btn btn-outline-secondary d-flex align-items-center px-4" id="previewBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                Preview
            </button>
            <button class="btn btn-primary d-flex align-items-center px-4" id="openConfirmModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                Queue Communication
            </button>
        </div>
    </div>
</div>

<!-- Suggestion Box for '#' trigger -->
<div id="tagSuggestions" class="card shadow-sm d-none" style="position:absolute; z-index:2000; width: 200px;">
    <div class="list-group list-group-flush">
        <button class="list-group-item list-group-item-action" data-tag="#branch_name#">Company Name</button>
        <button class="list-group-item list-group-item-action" data-tag="#BALANCE#">Balance</button>
        <button class="list-group-item list-group-item-action" data-tag="#kra_pin_number#">Account Number</button>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('build/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

<script>
    let editorInstance = null;
    let previewData = null;
    const sendBtn = $('#openConfirmModal');
    const channelSelect = document.getElementById('channel');
    const messageTextarea = document.getElementById('message');
    const suggestionBox = $('#tagSuggestions');

    // --- CKEditor Lifecycle ---
    async function initEditor() {
        if (editorInstance) return;
        try {
            editorInstance = await ClassicEditor.create(document.querySelector('#message'), {
                ckfinder: {
                    openerMethod: 'modal',
                    browseUrl: '/laravel-filemanager?type=Images',
                    uploadUrl: '/api/upload'
                }
            });
            // Update preview count when editor content changes
            editorInstance.model.document.on('change:data', () => {
                debouncedUpdateCount();
            });
        } catch (error) {
            console.error('CKEditor Error:', error);
        }
    }

    async function destroyEditor() {
        if (editorInstance) {
            await editorInstance.destroy();
            editorInstance = null;
        }
    }

    // --- Channel Change Handler ---
    async function handleChannelChange() {
        const channel = channelSelect.value;
        const subjectRow = $('#subjectRow');
        const timeRow = $('#timeToSendRow');
        const smsStats = $('#smsUnitStats');

        if (channel === 'email') {
            subjectRow.removeClass('d-none');
            timeRow.addClass('d-none');
            smsStats.addClass('d-none');
            await initEditor();
        } else {
            subjectRow.addClass('d-none');
            timeRow.removeClass('d-none');
            smsStats.removeClass('d-none');
            await destroyEditor();
        }
        updateRecipientPreview();
    }

    // --- Logic & Validation ---
    function getMessageContent() {
        return editorInstance ? editorInstance.getData() : $('#message').val();
    }

    $('#previewBtn').click(function () {
        const content = getMessageContent();
        if (!content) {
            Swal.fire('Empty Message', 'Please enter a message to preview.', 'info');
            return;
        }

        axios.post("{{ route('communication.preview') }}", { message: content })
            .then(res => {
                $('#previewContainer').removeClass('d-none');
                if (channelSelect.value === 'email') {
                    $('#previewBox').html(res.data.preview); // Render HTML for email
                } else {
                    $('#previewBox').text(res.data.preview); // Plain text for SMS
                }
            });
    });

    $('#openConfirmModal').on('click', function () {
        const channel = $('#channel').val();
        const content = getMessageContent();
        const subject = $('#subject').val();
        const time = $('#time_to_send').val();

        // 1. Validate Message
        if (!content || content.trim() === "") {
            Swal.fire('Message required', 'Please enter a message.', 'warning');
            return;
        }

        // 2. Validate Subject for Email
        if (channel === 'email' && !subject) {
            Swal.fire('Subject required', 'Please enter a subject for the email.', 'warning');
            return;
        }

        // 3. Validate Time for SMS
        if (channel === 'sms' && !time) {
            Swal.fire('Time required', 'Please select a time to send the SMS.', 'warning');
            return;
        }

        axios.post("{{ route('communication.previewCount') }}", {
            status: $('#account_status').val(),
            message: content
        }).then(res => {
            previewData = res.data;
            Swal.fire({
                title: 'Confirm Communication',
                html: `
                    <div class="text-start">
                        <p>You are about to queue a <strong>${channel.toUpperCase()}</strong> campaign:</p>
                        <ul>
                            <li><strong>Recipients:</strong> ${previewData.recipients}</li>
                            ${channel === 'sms' ? `<li><strong>Estimated Units:</strong> ${previewData.estimated_units}</li>` : ''}
                        </ul>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Queue Now',
                confirmButtonColor: '#083b36'
            }).then((result) => {
                if (result.isConfirmed) sendCommunication();
            });
        });
    });

    function sendCommunication() {
        sendBtn.prop('disabled', true).text('Processing...');
        
        axios.post("{{ route('communication.send') }}", {
            channel: $('#channel').val(),
            status: $('#account_status').val(),
            subject: $('#subject').val(),
            message: getMessageContent(),
            time_to_send: $('#time_to_send').val()
        })
        .then(res => {
            Swal.fire('Success!', 'Campaign has been queued successfully.', 'success');
            if (editorInstance) editorInstance.setData('');
            $('#message').val('');
            $('#subject').val('');
            $('#previewContainer').addClass('d-none');
        })
        .catch(() => Swal.fire('Error', 'Failed to queue campaign.', 'error'))
        .finally(() => sendBtn.prop('disabled', false).text('Queue Communication'));
    }

    function updateRecipientPreview() {
        const content = getMessageContent();
        if (!content || content.length < 5) {
            $('#recipientPreview').addClass('d-none');
            return;
        }

        axios.post("{{ route('communication.previewCount') }}", {
            status: $('#account_status').val(),
            message: content
        }).then(res => {
            $('#recipientPreview').removeClass('d-none');
            $('#recipientCount').text(res.data.recipients);
            $('#unitCount').text(res.data.estimated_units);
        });
    }

    // --- Merge Tags logic ---
    $(document).on('click', '.merge-badge', function () {
        const tag = $(this).data('tag');
        if (editorInstance) {
            const viewFragment = editorInstance.data.processor.toView(tag);
            const modelFragment = editorInstance.data.toModel(viewFragment);
            editorInstance.model.insertContent(modelFragment);
        } else {
            const el = messageTextarea;
            const start = el.selectionStart;
            const end = el.selectionEnd;
            el.value = el.value.substring(0, start) + tag + el.value.substring(end);
            el.selectionStart = el.selectionEnd = start + tag.length;
            el.focus();
        }
        updateRecipientPreview();
    });

    // --- Initialization ---
    let typingTimer;
    const debouncedUpdateCount = () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(updateRecipientPreview, 800);
    };

    $(document).ready(() => {
        handleChannelChange();
        $('#message').on('input', debouncedUpdateCount);
        $('#account_status').on('change', updateRecipientPreview);
    });

    channelSelect.addEventListener('change', handleChannelChange);

</script>
@endsection