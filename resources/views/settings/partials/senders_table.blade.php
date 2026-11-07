<tbody>
    @foreach ($senders as $sender)
        <tr>
            <td>{{ $sender->name }}</td>
            <td>{{ $sender->type }}</td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input sender-toggle-active" type="checkbox" data-id="{{ $sender->id }}" role="switch" id="isActive" @if($sender->active) checked @endif>
                </div>
            </td>
            <td>
                <div class="dropdown">
                    <a href="#" role="button" id="dropdownMenuLink5" data-bs-toggle="dropdown" aria-expanded="true" class="show">
                        <i class="ri-more-2-fill"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item delete-sender" href="#" data-sender="{{ $sender->name }}" data-id="{{ $sender->id }}">Remove</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
