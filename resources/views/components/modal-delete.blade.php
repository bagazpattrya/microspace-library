<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteForm" method="POST" class="modal-content border-0 shadow">
            @csrf @method('DELETE')
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini? Aksi ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#deleteModal').on('show.bs.modal', function(e) {
                var button = $(e.relatedTarget);
                var url = button.data('url');
                $('#deleteForm').attr('action', url);
            });
            $('#deleteForm').on('submit', function() {
                var btn = $(this).find('button[type="submit"]');
                btn.attr('disabled', true);
                btn.html(`
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Menghapus...
                `);
            });
        });
    </script>
@endpush
