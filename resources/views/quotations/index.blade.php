@extends('layouts.master')
@section('title') Quotations @endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Sales @endslot
    @slot('title') Quotations @endslot
@endcomponent

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card shadow-none border">
            <div class="card-body">
                <form action="{{ route('quotations.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Search Quotes</label>
                            <div class="search-box">
                                <input type="text" name="search" class="form-control search" placeholder="Quote # or Customer Name..." value="{{ request('search') }}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="invoiced" {{ request('status') == 'invoiced' ? 'selected' : '' }}>Invoiced</option>
                                <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="ri-filter-2-line me-1"></i> Filter</button>
                            <a href="{{ route('quotations.index') }}" class="btn btn-soft-secondary w-100">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Quotations List</h5>
                <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                    <i class="ri-add-line align-bottom me-1"></i> Create Quote
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Quote #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Amount (KES)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotations as $quote)
                            <tr>
                                <td><a href="{{ route('quotations.show', $quote->id) }}" class="fw-medium">{{ $quote->quote_number }}</a></td>
                                <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('d M, Y') }}</td>
                                <td>{{ $quote->customer->organization_name ?? $quote->customer->name }}</td>
                                <td><span class="fw-semibold">{{ number_format($quote->total_amount, 2) }}</span></td>
                                <td>
                                    @if($quote->status == 'invoiced')
                                        <span class="badge bg-success">INVOICED</span>
                                    @elseif($quote->status == 'sent')
                                        <span class="badge bg-primary">SENT</span>
                                    @elseif($quote->status == 'accepted')
                                        <span class="badge bg-info">ACCEPTED</span>
                                    @elseif($quote->status == 'declined')
                                        <span class="badge bg-danger">DECLINED</span>
                                    @else
                                        <span class="badge bg-secondary">DRAFT</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('quotations.show', $quote->id) }}" class="btn btn-sm btn-soft-info">View</a>
                                    @if($quote->status !== 'invoiced')
                                        <a href="{{ route('quotations.edit', $quote->id) }}" class="btn btn-sm btn-soft-primary">Edit</a>
                                        <button class="btn btn-sm btn-soft-danger" onclick="deleteQuote({{ $quote->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-4">No quotations found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $quotations->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function deleteQuote(id) {
        Swal.fire({
            title: 'Delete Quotation?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/quotations/${id}`)
                    .then(response => {
                        Swal.fire('Deleted!', response.data.success, 'success').then(() => location.reload());
                    })
                    .catch(error => {
                        Swal.fire('Error', error.response.data.message || 'Could not delete quotation.', 'error');
                    });
            }
        });
    }
</script>
@endsection