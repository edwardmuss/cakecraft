<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Invoice {{ $invoice->invoice_number }}</title>
    <link href="{{ asset('build/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('build/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('build/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <style>
        body { background-color: #f3f6f9; }
        .checkout-box { max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .logo { max-height: 60px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="checkout-box text-center">
        @if(true)
            <img src="{{ \App\Support\Brand::logoUrl() }}" alt="Logo" class="logo">
        @endif
        
        <h4 class="mb-1">Invoice {{ $invoice->invoice_number }}</h4>
        <p class="text-muted mb-4">{{ $invoice->branch->branch_name }}</p>

        <div class="bg-light p-3 rounded mb-4 text-start">
            <h6 class="fs-14 mb-2 border-bottom pb-2">Order Summary</h6>
            <ul class="list-unstyled mb-0 fs-13">
                @foreach($invoice->items as $item)
                    <li class="d-flex justify-content-between mb-1">
                        <span>{{ $item->quantity }}PCs {{ $item->item_name }}</span>
                        <span>KES {{ number_format($item->total, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        @php
            $balance = $invoice->total_amount - $invoice->amount_paid;
        @endphp

        <h1 class="text-success mb-4">KES {{ number_format($balance, 2) }}</h1>

        <div id="payment-ui-area">
            <form id="stkPushForm" class="text-start needs-validation" novalidate>
                <input type="hidden" name="account_number" id="account_number" value="{{ $invoice->invoice_number }}">
                <input type="hidden" name="amount" id="mpesa_amount" value="{{ $balance }}">
                
                <div class="mb-3">
                    <label class="form-label">M-Pesa Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-success text-white border-success"><i class="ri-phone-line"></i></span>
                        <input type="text" class="form-control border-success" id="mpesa_number" name="mpesa_number" value="{{ $invoice->customer->phone }}" placeholder="2547..." required>
                    </div>
                    <small class="text-muted mt-1 d-block">An STK prompt will appear on this phone.</small>
                </div>

                <button type="submit" id="payBtn" class="btn btn-success w-100 btn-lg shadow-sm">
                    Pay with M-Pesa
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-start fs-13 text-muted">
                <strong>Manual Payment:</strong><br>
                If the prompt doesn't appear, go to M-Pesa > Lipa na M-Pesa > Paybill.<br>
                Business No: <strong>7714141</strong><br>
                Account No: <strong>{{ $invoice->invoice_number }}</strong>
            </div>
        </div>
        
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    // Setup Axios to handle CSRF
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}'; // Always good to include just in case

    document.getElementById('stkPushForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formElement = this;
        
        // Basic HTML5 validation
        if (!formElement.checkValidity()) {
            formElement.classList.add('was-validated');
            return;
        }

        const submitBtn = document.getElementById('payBtn');
        const phone = document.getElementById('mpesa_number').value;
        
        // Use FormData exactly like the internal index page
        const formData = new FormData(formElement);

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

        // NOTE: Make sure this route is unprotected in web.php!
        axios.post('{{ route("payments.public_topup") }}', formData)
        .then(function(response) {
            if (response.data.success) {
                // Swap the UI to show the Processing Lordicon
                document.getElementById('payment-ui-area').innerHTML = `
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <lord-icon src="https://cdn.lordicon.com/rqptwppx.json" trigger="loop" colors="primary:#198754,secondary:#198754" style="width:120px;height:120px"></lord-icon>
                        </div>
                        <h5>Processing Payment...</h5>
                        <p class="text-muted">Please enter your M-Pesa PIN on your phone (<strong>${phone}</strong>).</p>
                    </div>
                `;

                // Start polling!
                pollTransactionStatus(response.data.CheckoutRequestID, response.data.MerchantRequestID);
            } else {
                Swal.fire({
                    title: 'Failed',
                    html: response.data.message + (response.data.error ? `<br><b>Error:</b> ${response.data.error}` : ''),
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
                resetBtn(submitBtn);
            }
        })
        .catch(function(error) {
            Swal.fire('Error', 'An error occurred while connecting to M-Pesa.', 'error');
            resetBtn(submitBtn);
        });
    });

    function pollTransactionStatus(checkoutRequestId, merchantRequestId) {
        const interval = setInterval(function() {
            // NOTE: Make sure this route is unprotected in web.php!
            axios.post('{{ route("payments.public_status") }}', {
                CheckoutRequestID: checkoutRequestId,
                MerchantRequestID: merchantRequestId,
            })
            .then(function(response) {
                if (response.data.success) {
                    const status = response.data.status;

                    if (status === 'confirmed') {
                        clearInterval(interval);
                        // Swap UI to show Success Lordicon
                        document.getElementById('payment-ui-area').innerHTML = `
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#198754,secondary:#198754" style="width:120px;height:120px"></lord-icon>
                                </div>
                                <h5 class="text-success">Payment Successful!</h5>
                                <p class="text-muted">Your payment was received and applied to the invoice.</p>
                                <h4 class="fw-bold mb-4">Receipt: <span class="text-success">${response.data.receipt_number}</span></h4>
                                <a href="{{ route('invoices.public_pdf', $invoice->id) }}" class="btn btn-primary w-100 btn-lg shadow-sm">Download Receipt PDF</a>
                            </div>
                        `;
                    } else if (status !== 'pending') {
                        clearInterval(interval);
                        // Swap UI to show Error Lordicon
                        document.getElementById('payment-ui-area').innerHTML = `
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#dc3545,secondary:#dc3545" style="width:120px;height:120px"></lord-icon>
                                </div>
                                <h5 class="text-danger">Transaction Failed / Cancelled</h5>
                                <p class="text-muted">${response.data.message}</p>
                                <button class="btn btn-soft-danger w-100 mt-3" onclick="location.reload()">Try Again</button>
                            </div>
                        `;
                    }
                }
            })
            .catch(function(error) {
                console.error('Polling error:', error);
            });
        }, 5000); 
    }

    function resetBtn(btn) {
        btn.disabled = false;
        btn.innerHTML = 'Pay with M-Pesa';
    }
</script>
</body>
</html>