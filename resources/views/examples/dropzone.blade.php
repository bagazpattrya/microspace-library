<form>
    <x-dropzone id="invoiceDropzone" name="invoice" label="Bukti Pembayaran" accept="image/*,application/pdf" required />
</form>

@push('scripts')
<script>
    // Dropzone dimuat dari CDN (dropzone@5.9.3); komponen hanya merender kotaknya.
    Dropzone.autoDiscover = false;
    new Dropzone('#invoiceDropzone', { url: '/upload', autoProcessQueue: false, maxFiles: 1, addRemoveLinks: true });
</script>
@endpush
