<tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->phone }}</td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input toggle-active" type="checkbox" data-id="{{ $user->id }}" role="switch" id="isActive" @if($user->isActive) checked @endif>
                </div>
            </td>
            <td>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.permissions.edit', $user) }}" 
                    class="btn btn-soft-success btn-sm" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="@lang('translation.permissions')">
                        <i class="ri-fingerprint-line"></i>
                    </a>

                    <button type="button" 
                            class="btn btn-soft-info btn-sm edit-user" 
                            onclick="showEditModal({{ $user }})" 
                            data-id="{{ $user->id }}"
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Edit">
                        <i class="ri-edit-line"></i>
                    </button>

                    <button type="button" 
                            class="btn btn-soft-danger btn-sm delete-user" 
                            data-id="{{ $user->id }}"
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="Delete">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
