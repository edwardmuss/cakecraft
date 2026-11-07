@extends('layouts.master')
@section('title') Products @endsection 
@section('css')
    {{-- Css for products --}}
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Inventory @endslot
    @slot('title') Products @endslot
@endcomponent

<div class="row gy-2 mb-2">
    @include('includes.flash_message')
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Product List</h5>
                <button type="button" class="btn btn-primary" onclick="openModal()">
                    <i class="ri-add-line align-bottom me-1"></i> Add Product
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Unit Price (KES)</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            @foreach($products as $product)
                            <tr id="row_{{ $product->id }}">
                                <td>{{ $product->name }}</td>
                                <td>{{ Str::limit($product->description, 40) ?? 'N/A' }}</td>
                                <td>{{ number_format($product->unit_price, 2) }}</td>
                                <td>
                                    @if($product->track_stock)
                                        @if($product->current_stock <= $product->reorder_level)
                                            <span class="badge bg-danger-subtle text-danger" id="stock_display_{{ $product->id }}">{{ $product->current_stock }} (Low)</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success" id="stock_display_{{ $product->id }}">{{ $product->current_stock }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-soft-info" onclick="editProduct({{ $product->id }})">Edit</button>
                                    @if($product->track_stock)
                                        <button class="btn btn-sm btn-soft-warning" onclick="openStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->current_stock }})">Stock</button>
                                    @endif
                                    <button class="btn btn-sm btn-soft-danger" onclick="deleteProduct({{ $product->id }})">Delete</button>
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

<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="productForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="productId" name="id">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="unit_price" class="form-label">Unit Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" required>
                        <span class="text-danger error-text unit_price_error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <span class="text-danger error-text description_error"></span>
                    </div>

                    <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">Active (Available for sale)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="stockForm">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock: <span id="display_product_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="stock_product_id">
                    
                    <div class="alert alert-info">Current Stock: <strong id="display_current_stock"></strong></div>

                    <div class="mb-3">
                        <label class="form-label">Action <span class="text-danger">*</span></label>
                        <select class="form-select" id="stock_type" required>
                            <option value="addition">Add Stock (Receive Shipment)</option>
                            <option value="deduction">Deduct Stock (Damage/Loss)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stock_quantity" min="1" required>
                        <span class="text-danger error-text quantity_error"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference / Notes</label>
                        <input type="text" class="form-control" id="stock_reference" placeholder="e.g. Supplier XYZ delivery">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveStockBtn">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const productModal = new bootstrap.Modal(document.getElementById('productModal'));

    function openModal() {
        document.getElementById('productForm').reset();
        document.getElementById('productId').value = '';
        document.getElementById('productModalLabel').innerText = 'Add Product';
        document.getElementById('is_active').checked = true; // Default to active
        $('.error-text').text(''); 
        productModal.show();
    }

    function editProduct(id) {
        $('.error-text').text('');
        axios.get(`/products/${id}/edit`)
            .then(response => {
                const product = response.data;
                document.getElementById('productId').value = product.id;
                document.getElementById('name').value = product.name;
                document.getElementById('unit_price').value = product.unit_price;
                document.getElementById('description').value = product.description || '';
                document.getElementById('is_active').checked = product.is_active ? true : false;
                
                document.getElementById('productModalLabel').innerText = 'Edit Product';
                productModal.show();
            })
            .catch(error => {
                Swal.fire('Error', 'Could not fetch product data', 'error');
            });
    }

    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let id = document.getElementById('productId').value;
        let url = id ? `/products/${id}` : '/products';
        let method = id ? 'PUT' : 'POST';
        
        let formData = {
            name: document.getElementById('name').value,
            unit_price: document.getElementById('unit_price').value,
            description: document.getElementById('description').value,
            is_active: document.getElementById('is_active').checked ? 1 : 0,
        };

        document.getElementById('saveBtn').disabled = true;
        $('.error-text').text(''); 

        axios({
            method: method,
            url: url,
            data: formData
        })
        .then(response => {
            productModal.hide();
            Swal.fire('Success', response.data.success, 'success').then(() => {
                location.reload(); 
            });
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
            document.getElementById('saveBtn').disabled = false;
        });
    });

    function deleteProduct(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will remove the product from the catalog.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/products/${id}`)
                    .then(response => {
                        Swal.fire('Deleted!', response.data.success, 'success');
                        document.getElementById('row_' + id).remove();
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Could not delete product.', 'error');
                    });
            }
        });
    }

    const stockModal = new bootstrap.Modal(document.getElementById('stockModal'));

    function openStockModal(id, name, currentStock) {
        document.getElementById('stockForm').reset();
        document.getElementById('stock_product_id').value = id;
        document.getElementById('display_product_name').innerText = name;
        document.getElementById('display_current_stock').innerText = currentStock;
        $('.error-text').text('');
        stockModal.show();
    }

    document.getElementById('stockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let id = document.getElementById('stock_product_id').value;
        let formData = {
            type: document.getElementById('stock_type').value,
            quantity: document.getElementById('stock_quantity').value,
            reference: document.getElementById('stock_reference').value,
        };

        document.getElementById('saveStockBtn').disabled = true;

        axios.post(`/products/${id}/stock`, formData)
            .then(response => {
                stockModal.hide();
                Swal.fire('Success', response.data.success, 'success').then(() => {
                    location.reload(); 
                });
            })
            .catch(error => {
                Swal.fire('Error', 'Could not update stock.', 'error');
            })
            .finally(() => {
                document.getElementById('saveStockBtn').disabled = false;
            });
    });
</script>
@endsection