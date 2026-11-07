{{-- Drag-and-drop ordering. Usage: @include('includes.sortable', ['url' => route('sizes.reorder')]) --}}
<style>
    .cc-handle { width: 36px; color: #98a2b3; cursor: grab; text-align: center; }
    .cc-handle:active { cursor: grabbing; }
    tr.cc-ghost { opacity: .45; background: #fdf2f4; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const body = document.getElementById('sortRows');
    if (!body || typeof Sortable === 'undefined') return;
    Sortable.create(body, {
        handle: '.cc-handle',
        draggable: 'tr[data-id]',
        animation: 150,
        ghostClass: 'cc-ghost',
        onEnd: function () {
            const ids = [...body.querySelectorAll('tr[data-id]')].map(r => r.dataset.id);
            ids.forEach((id, i) => {
                const cell = body.querySelector(`tr[data-id="${id}"] .cc-order`);
                if (cell) cell.textContent = i;
            });
            axios.post(@json($url), { ids: ids })
                .catch(() => Swal.fire('Error', 'Could not save the new order. Please refresh and try again.', 'error'));
        }
    });
})();
</script>
