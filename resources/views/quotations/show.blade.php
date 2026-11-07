@extends('layouts.master')
@section('title') Quote {{ $quotation->quote_number }} @endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Quotations @endslot
    @slot('title') Details @endslot
@endcomponent

<!-- Zoho Vibe: What's Next Banner -->
@if($quotation->status !== 'invoiced')
<div class="row mb-3">
    <div class="col-12">
        <div class="card border border-primary shadow-none bg-primary-subtle">
            <div class="card-body d-flex align-items-center justify-content-between py-2">
                <div>
                    <span class="fs-15 fw-semibold text-primary">✨ WHAT'S NEXT?</span>
                    <span class="text-muted ms-2">Create an invoice for this quote to confirm the sale and bill your customer.</span>
                </div>
                <form action="{{ route('quotations.convert', $quotation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to convert this quote to a live invoice?')">
                        Convert to Invoice
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 flex-grow-1">
                    {{ $quotation->quote_number }}
                    @if($quotation->status == 'invoiced')
                        <span class="badge bg-success ms-2 align-middle">INVOICED</span>
                    @elseif($quotation->status == 'sent')
                        <span class="badge bg-primary ms-2 align-middle">SENT</span>
                    @else
                        <span class="badge bg-secondary ms-2 align-middle text-uppercase">{{ $quotation->status }}</span>
                    @endif
                </h4>
                <div class="flex-shrink-0 d-flex gap-2">
                    @if($quotation->status !== 'invoiced')
                        <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-soft-primary">
                            <i class="ri-pencil-fill align-bottom me-1"></i> Edit
                        </a>
                    @endif
                    
                    <a href="{{ route('quotations.pdf', $quotation->id) }}" target="_blank" class="btn btn-soft-secondary">
                        <i class="ri-printer-line align-bottom me-1"></i> Print / PDF
                    </a>
                </div>
            </div>

            <!-- Tabs -->
            <div class="card-body border-bottom border-bottom-dashed">
                <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#quote-details" role="tab">
                            Quote Details
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#related-invoices" role="tab">
                            Invoices @if($quotation->invoices->count() > 0) <span class="badge bg-danger align-middle ms-1">{{ $quotation->invoices->count() }}</span> @endif
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    
                    <!-- TAB 1: QUOTE DETAILS -->
                    <div class="tab-pane active" id="quote-details" role="tabpanel">
                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Quote Information</h6>
                        <div class="row mb-4">
                            <div class="col-sm-3 col-6 mb-3">
                                <p class="text-muted mb-1">Quote Date</p>
                                <h6 class="fs-14 mb-0">{{ \Carbon\Carbon::parse($quotation->quote_date)->format('d M, Y') }}</h6>
                            </div>
                            <div class="col-sm-3 col-6 mb-3">
                                <p class="text-muted mb-1">Expiry Date</p>
                                <h6 class="fs-14 mb-0">{{ $quotation->expiry_date ? \Carbon\Carbon::parse($quotation->expiry_date)->format('d M, Y') : 'N/A' }}</h6>
                            </div>
                            <div class="col-sm-6 col-12 mb-3">
                                <p class="text-muted mb-1">Subject / Project</p>
                                <h6 class="fs-14 mb-0 text-primary">{{ $quotation->subject ?? 'Standard Quotation' }}</h6>
                            </div>
                        </div>

                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Customer Details</h6>
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <p class="text-muted mb-1">Name / Organization</p>
                                <h6 class="fs-14 mb-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs flex-shrink-0 me-2">
                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-13">
                                                {{ substr($quotation->customer->organization_name ?? $quotation->customer->name, 0, 1) }}
                                            </div>
                                        </div>
                                        {{ $quotation->customer->organization_name ?? $quotation->customer->name }}
                                    </div>
                                </h6>
                            </div>
                            <div class="col-sm-6 mt-3 mt-sm-0">
                                <p class="text-muted mb-1">Billing Address</p>
                                <h6 class="fs-14 mb-0 text-muted fw-normal">{{ $quotation->customer->billing_address ?? 'Not provided' }}</h6>
                            </div>
                        </div>

                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Items <span class="badge bg-light text-dark align-middle">{{ $quotation->items->count() }}</span></h6>
                        <div class="table-responsive">
                            <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                <thead>
                                    <tr class="table-active">
                                        <th scope="col" class="text-start">Item Description</th>
                                        <th scope="col">Rate (KES)</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col" class="text-end">Amount (KES)</th>
                                    </tr>
                                </thead>
                                <tbody id="products-list">
                                    @foreach($quotation->items as $item)
                                    <tr>
                                        <td class="text-start"><span class="fw-medium">{{ $item->item_name }}
                        @if($item->size_label || $item->flavour_name)
                            <br><small class="text-muted">{{ collect([$item->size_label, $item->flavour_name])->filter()->implode(' · ') }}</small>
                        @endif
                        @if($item->cake_message)
                            <br><small class="text-muted">Message: {{ $item->cake_message }}</small>
                        @endif</span></td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="border-top border-top-dashed mt-2">
                            <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width:250px">
                                <tbody>
                                    <tr>
                                        <td>Sub Total</td>
                                        <td class="text-end">{{ number_format($quotation->subtotal, 2) }}</td>
                                    </tr>
                                    @if($quotation->tax_amount > 0)
                                    <tr>
                                        <td>Tax (VAT)</td>
                                        <td class="text-end">{{ number_format($quotation->tax_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($quotation->shipping_fee > 0)
                                    <tr>
                                        <td>Shipping</td>
                                        <td class="text-end">{{ number_format($quotation->shipping_fee, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr class="border-top border-top-dashed fs-15">
                                        <th scope="row">Grand Total</th>
                                        <th class="text-end">KES {{ number_format($quotation->total_amount, 2) }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if($quotation->terms_and_conditions)
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-semibold">Terms and Conditions</h6>
                            <p class="text-muted mb-0">{{ $quotation->terms_and_conditions }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- TAB 2: RELATED INVOICES -->
                    <div class="tab-pane" id="related-invoices" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date Created</th>
                                        <th>Invoice #</th>
                                        <th>Status</th>
                                        <th>Total (KES)</th>
                                        <th>Balance Due (KES)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quotation->invoices as $inv)
                                    <tr>
                                        <td>{{ $inv->created_at->format('d M, Y') }}</td>
                                        <td><a href="{{ route('invoices.show', $inv->id) }}" class="fw-medium text-primary">{{ $inv->invoice_number }}</a></td>
                                        <td>
                                            @if($inv->status == 'paid') <span class="badge text-success">PAID</span>
                                            @else <span class="badge text-warning text-uppercase">{{ str_replace('_', ' ', $inv->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($inv->total_amount, 2) }}</td>
                                        <td>{{ number_format($inv->total_amount - $inv->amount_paid, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted p-4">No invoices have been generated from this quote yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection