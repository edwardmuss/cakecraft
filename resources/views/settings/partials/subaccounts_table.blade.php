<tbody>
    @foreach ($subaccounts as $subaccount)
        <tr>
            <td>{{ $subaccount->branch_name }}</td>
            <td>{{ $subaccount->branch_email }}</td>
            <td>{{ $subaccount->company_phone }}</td>
            <td>{{ $subaccount->balance_kes }}</td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input subaccount-toggle-active" type="checkbox" 
                           data-id="{{ $subaccount->id }}" role="switch" 
                           @if($subaccount->isActive) checked @endif>
                </div>
            </td>
            <td>
                <div class="d-flex gap-2">
                    {{-- View Button --}}
                    <a href="{{ route('settings.subaccounts', $subaccount->id) }}" 
                       class="btn btn-soft-primary btn-sm" 
                       data-bs-toggle="tooltip" title="View">
                        <i class="ri-eye-line"></i>
                    </a>

                    {{-- Edit Button --}}
                    <button type="button" 
                            class="btn btn-soft-info btn-sm edit-subaccount" 
                            onclick="showSubAccountEditModal({{ $subaccount }})"
                            data-bs-toggle="tooltip" title="Edit">
                        <i class="ri-edit-line"></i>
                    </button>

                    {{-- Delete Button --}}
                    <button type="button" 
                            class="btn btn-soft-danger btn-sm delete-subaccount" 
                            data-id="{{ $subaccount->id }}"
                            data-bs-toggle="tooltip" title="Delete">
                        <i class="ri-delete-bin-3-line"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>