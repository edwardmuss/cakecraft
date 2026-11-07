{{-- Shared by invoices.create and invoices.edit. Expects: $customers, $catalogue, $branch, $occasionTypes, $invoice (nullable) --}}
@php
    $isEdit = isset($invoice) && $invoice;
    $val = fn($k, $d = null) => $isEdit ? ($invoice->{$k} ?? $d) : $d;
    $existingItems = $isEdit ? $invoice->items->map(fn($i) => [
        'product_id' => $i->product_id, 'item_name' => $i->item_name, 'cake_size_id' => $i->cake_size_id,
        'flavour_name' => $i->flavour_name, 'cake_message' => $i->cake_message,
        'quantity' => $i->quantity, 'unit_price' => (float) $i->unit_price,
    ])->values() : collect();
@endphp

<form id="invoiceForm">
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="cc-section-title">Customer &amp; occasion</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer *</label>
                            <select class="form-control select2" id="customer_id" required>
                                <option value="">Select customer</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" @selected($val('customer_id') == $c->id)>{{ $c->name }}{{ $c->organization_name ? ' (' . $c->organization_name . ')' : '' }} · {{ $c->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Occasion</label>
                            <select class="form-select" id="occasion_type">
                                <option value="">None</option>
                                @foreach($occasionTypes as $k => $l)<option value="{{ $k }}" @selected($val('occasion_type') == $k)>{{ $l }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cake needed on</label>
                            <input type="date" class="form-control" id="occasion_date" value="{{ $isEdit && $invoice->occasion_date ? \Carbon\Carbon::parse($invoice->occasion_date)->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Celebrant / recipient</label>
                            <input type="text" class="form-control" id="recipient_name" value="{{ $val('recipient_name') }}" placeholder="e.g. Brian">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pickup or delivery</label>
                            <select class="form-select" id="fulfilment_type">
                                <option value="pickup" @selected($val('fulfilment_type', 'pickup') == 'pickup')>Pickup</option>
                                <option value="delivery" @selected($val('fulfilment_type') == 'delivery')>Delivery</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment due</label>
                            <input type="date" class="form-control" id="due_date" value="{{ $isEdit && $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-12" id="addrWrap" style="display:none">
                            <label class="form-label">Delivery address</label>
                            <input type="text" class="form-control" id="delivery_address" value="{{ $val('delivery_address') }}">
                        </div>
                    </div>
                    <p class="text-muted small mt-3 mb-0"><i class="ri-information-line"></i> Birthday and anniversary orders are remembered so you can remind the customer next year.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="cc-section-title">Cakes</div>
                    <div class="table-responsive">
                        <table class="table cc-table" id="itemsTable">
                            <thead><tr>
                                <th style="min-width:210px">Cake</th>
                                <th style="min-width:120px">Size</th>
                                <th style="min-width:140px">Flavour</th>
                                <th style="min-width:160px">Message on cake</th>
                                <th style="width:80px">Qty</th>
                                <th style="width:120px" class="text-end">Price</th>
                                <th style="width:110px" class="text-end">Total</th>
                                <th style="width:40px"></th>
                            </tr></thead>
                            <tbody id="invoiceItems"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-soft-primary btn-sm" id="addRowBtn"><i class="ri-add-line"></i> Add cake</button>
                    <button type="button" class="btn btn-light btn-sm" id="addCustomBtn">Add custom item</button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <label class="form-label">Notes / payment terms</label>
                    <textarea class="form-control" id="notes" rows="2" placeholder="Thank you for choosing Cakecraft.">{{ $val('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card" style="position:sticky;top:90px">
                <div class="card-body">
                    <div class="cc-section-title">Summary</div>
                    <table class="table table-borderless table-sm mb-3">
                        <tr><td>Subtotal</td><td class="text-end"><span id="display_subtotal">0.00</span></td></tr>
                        <tr><td>VAT ({{ $branch->vat_percentage ?? 0 }}%)</td><td class="text-end"><span id="display_tax">0.00</span></td></tr>
                        <tr><td class="align-middle">Delivery zone</td><td class="text-end">
                            <select class="form-select form-select-sm ms-auto" id="zone_pick" style="max-width:150px">
                                <option value="">Custom</option>
                                @foreach(\App\Models\DeliveryZone::where('branch_id', $branch->id)->where('is_active', true)->orderBy('sort_order')->get() as $zn)
                                    <option value="{{ $zn->fee }}">{{ $zn->name }}</option>
                                @endforeach
                            </select>
                        </td></tr>
                        <tr><td class="align-middle">Delivery fee</td><td class="text-end">
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end ms-auto" id="shipping_fee" value="{{ $val('shipping_fee', '0.00') }}" style="max-width:110px">
                        </td></tr>
                        <tr class="border-top"><td class="pt-2 fs-16"><strong>Total (KES)</strong></td><td class="text-end pt-2 fs-16"><strong id="display_total">0.00</strong></td></tr>
                    </table>
                    <button type="submit" class="btn btn-primary w-100" id="saveInvoiceBtn">{{ $isEdit ? 'Update Invoice' : 'Create Invoice' }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

@section('script')
<script>
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const CATALOGUE = @json($catalogue);
    const EXISTING = @json($existingItems);
    const vatPercentage = {{ $branch->vat_percentage ?? 0 }};
    const IS_EDIT = {{ $isEdit ? 'true' : 'false' }};
    const fmt = n => (Number(n) || 0).toFixed(2);

    function cakeById(id) { return CATALOGUE.find(c => String(c.id) === String(id)); }

    function rowTemplate(custom) {
        const cakeCell = custom
            ? `<input type="text" class="form-control form-control-sm row-name" placeholder="Describe item">`
            : `<select class="form-select form-select-sm row-cake"><option value="">Select cake</option>${CATALOGUE.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}</select>`;
        return $(`<tr class="item-row" data-custom="${custom ? 1 : 0}">
            <td>${cakeCell}</td>
            <td><select class="form-select form-select-sm row-size" ${custom ? 'disabled' : ''}></select></td>
            <td><select class="form-select form-select-sm row-flavour" ${custom ? 'disabled' : ''}></select></td>
            <td><input type="text" class="form-control form-control-sm row-msg" maxlength="150" placeholder="Happy Birthday..."></td>
            <td><input type="number" class="form-control form-control-sm row-qty" min="1" value="1"></td>
            <td><input type="number" step="0.01" min="0" class="form-control form-control-sm text-end row-price" value="0.00"></td>
            <td class="text-end row-total-cell">0.00</td>
            <td><button type="button" class="btn btn-sm btn-soft-danger remove-row"><i class="ri-delete-bin-line"></i></button></td>
        </tr>`);
    }

    function fillOptions(row) {
        const cake = cakeById(row.find('.row-cake').val());
        const size = row.find('.row-size'), fl = row.find('.row-flavour');
        size.empty(); fl.empty();
        if (!cake) { size.append('<option value="">-</option>'); fl.append('<option value="">-</option>'); return; }
        if (cake.sizes.length) cake.sizes.forEach(s => size.append(`<option value="${s.id}">${s.label}</option>`));
        else size.append('<option value="">Standard</option>');
        if (cake.flavours.length) cake.flavours.forEach(f => fl.append(`<option value="${f.id}">${f.name}${f.extra > 0 ? ' (+' + f.extra + ')' : ''}</option>`));
        else fl.append('<option value="">-</option>');
    }

    function autoPrice(row) {
        const cake = cakeById(row.find('.row-cake').val());
        if (!cake) return;
        let price = cake.price;
        const sid = row.find('.row-size').val();
        const s = cake.sizes.find(x => String(x.id) === String(sid));
        if (s) price = s.price;
        const fid = row.find('.row-flavour').val();
        const f = cake.flavours.find(x => String(x.id) === String(fid));
        if (f) price += f.extra;
        row.find('.row-price').val(fmt(price));
    }

    function recalc() {
        let subtotal = 0;
        $('.item-row').each(function () {
            const qty = parseFloat($(this).find('.row-qty').val()) || 0;
            const price = parseFloat($(this).find('.row-price').val()) || 0;
            const t = qty * price; subtotal += t;
            $(this).find('.row-total-cell').text(fmt(t));
        });
        const ship = parseFloat($('#shipping_fee').val()) || 0;
        const tax = subtotal * vatPercentage / 100;
        $('#display_subtotal').text(fmt(subtotal)); $('#display_tax').text(fmt(tax)); $('#display_total').text(fmt(subtotal + tax + ship));
    }

    function addRow(custom, data) {
        const row = rowTemplate(custom);
        $('#invoiceItems').append(row);
        if (data) {
            if (custom || !data.product_id) { /* custom line */ row.find('.row-name').val(data.item_name); }
            else {
                row.find('.row-cake').val(data.product_id); fillOptions(row);
                if (data.cake_size_id) row.find('.row-size').val(data.cake_size_id);
                const cake = cakeById(data.product_id);
                const fl = cake && cake.flavours.find(f => f.name === data.flavour_name);
                if (fl) row.find('.row-flavour').val(fl.id);
            }
            row.find('.row-msg').val(data.cake_message || '');
            row.find('.row-qty').val(data.quantity);
            row.find('.row-price').val(fmt(data.unit_price));
        } else { fillOptions(row); }
        recalc();
    }

    $(function () {
        $('.select2').select2();
        if (IS_EDIT && EXISTING.length) EXISTING.forEach(i => addRow(!i.product_id, i)); else addRow(false);
        const toggleAddr = () => $('#addrWrap').toggle($('#fulfilment_type').val() === 'delivery');
        $('#fulfilment_type').on('change', toggleAddr); toggleAddr();
    });

    $('#zone_pick').on('change', function () { if (this.value !== '') { $('#shipping_fee').val(parseFloat(this.value).toFixed(2)); recalc(); } });
    $('#addRowBtn').on('click', () => addRow(false));
    $('#addCustomBtn').on('click', () => addRow(true));
    $(document).on('click', '.remove-row', function () {
        if ($('.item-row').length > 1) { $(this).closest('tr').remove(); recalc(); }
        else Swal.fire('Warning', 'You must have at least one item.', 'warning');
    });
    $(document).on('change', '.row-cake', function () { const r = $(this).closest('tr'); fillOptions(r); autoPrice(r); recalc(); });
    $(document).on('change', '.row-size, .row-flavour', function () { autoPrice($(this).closest('tr')); recalc(); });
    $(document).on('input', '.row-qty, .row-price, #shipping_fee', recalc);

    $('#invoiceForm').on('submit', function (e) {
        e.preventDefault();
        const items = [];
        $('.item-row').each(function () {
            const r = $(this), custom = r.data('custom') == 1;
            const qty = parseInt(r.find('.row-qty').val()) || 0;
            if (qty < 1) return;
            const base = { quantity: qty, unit_price: parseFloat(r.find('.row-price').val()) || 0, cake_message: r.find('.row-msg').val() };
            if (custom) { const n = r.find('.row-name').val(); if (n) items.push({ ...base, item_name: n }); }
            else {
                const pid = r.find('.row-cake').val(); if (!pid) return;
                items.push({ ...base, product_id: pid, cake_size_id: r.find('.row-size').val() || null, flavour_id: r.find('.row-flavour').val() || null });
            }
        });
        if (!$('#customer_id').val()) return Swal.fire('Error', 'Please select a customer.', 'error');
        if (!items.length) return Swal.fire('Error', 'Please add at least one cake.', 'error');

        const payload = {
            customer_id: $('#customer_id').val(), due_date: $('#due_date').val() || null,
            occasion_type: $('#occasion_type').val() || null, occasion_date: $('#occasion_date').val() || null,
            recipient_name: $('#recipient_name').val() || null, fulfilment_type: $('#fulfilment_type').val(),
            delivery_address: $('#delivery_address').val() || null, notes: $('#notes').val(),
            shipping_fee: parseFloat($('#shipping_fee').val()) || 0, items
        };
        const btn = $('#saveInvoiceBtn').prop('disabled', true).text('Saving...');
        const req = IS_EDIT ? axios.put('{{ $isEdit ? route('invoices.update', $invoice->id) : '' }}', payload) : axios.post('{{ route('invoices.store') }}', payload);
        req.then(res => Swal.fire('Success', res.data.success, 'success').then(() => window.location.href = res.data.redirect_url))
           .catch(err => {
               const m = err.response?.data?.errors ? Object.values(err.response.data.errors).flat().join('\n') : 'Failed to save. Check inputs.';
               Swal.fire('Error', m, 'error'); btn.prop('disabled', false).text(IS_EDIT ? 'Update Invoice' : 'Create Invoice');
           });
    });
</script>
@endsection
