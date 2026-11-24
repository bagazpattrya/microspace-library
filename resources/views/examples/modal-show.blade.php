<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#showModal">Lihat Detail</button>

<x-modal-show />

@push('scripts')
<script>
    // isi #modal-body-content sesuai kebutuhan, mis. hasil fetch()
    document.getElementById('showModal').addEventListener('show.bs.modal', () => {
        document.getElementById('modal-body-content').innerHTML = '<p class="mb-0">Isi detail di sini.</p>';
    });
</script>
@endpush
