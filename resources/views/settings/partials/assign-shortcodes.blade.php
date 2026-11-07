<table class="table">
    <thead>
        <th>Sender</th>
        <th>Type</th>
        <th>Active</th>
        <th>Assign</th>
    </thead>
    <tbody>
        @foreach($shortcodes as $shortcode)
            <tr>
                <td>{{ $shortcode->name }}</td>
                <td>{{ $shortcode->type }}</td>
                <td>
                    <div class="badge {{ $shortcode->active ? "bg-success" : "bg-danger" }}">{{ $shortcode->active ? "Active" : "Inactive" }}</div>
                </td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input sender-toggle-assign" type="checkbox"
                            data-id="{{ $shortcode->id }}" role="switch" id="assignSender{{ $shortcode->id }}"
                            @if(in_array($shortcode->id, $assignedShortcodes)) checked @endif>
                        <label class="form-check-label"> Yes</label>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
