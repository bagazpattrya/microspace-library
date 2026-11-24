<div class="modal fade" id="editNominalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="editNominalForm">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title">Update Nominal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Nominal</label>
                    <input type="text" id="nominalInput" class="form-control bg-dark" placeholder="Rp 0" required>
                    <input type="hidden" name="amount" id="nominalRaw">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-primary" data-loading-button>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        <span class="btn-text">Update</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="cancelPaymentForm">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Batalkan Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-2">
                        Anda akan <strong>membatalkan pembayaran ini!</strong>
                    </p>
                    <ul class="text-muted small">
                        <li class="ms-1">- Status pembayaran kembali <strong>&nbsp;Belum Lunas</strong></li>
                        <li class="ms-1">- Transaksi terkait akan <strong>&nbsp;dihapus</strong></li>
                    </ul>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Kembali
                    </button>
                    <button type="submit" class="btn btn-danger" data-loading-button>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        <span class="btn-text">Lanjut</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- PAY MODAL --}}
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" enctype="multipart/form-data" class="modal-content paymentPayForm" id="payForm">
            @csrf
            @method('POST')

            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- PAYMENT DATE --}}
                <div class="mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="paid_at" class="form-control bg-dark"
                        value="{{ now()->toDateString() }}" required>
                </div>

                <div class="mb-4">
                    <label class="form-label d-block mb-2">Payment Method</label>

                    <div class="payment-method-group">
                        <input type="radio" name="payment_method" id="method_cash" value="cash" checked>
                        <label for="method_cash">
                            💵 Cash
                        </label>

                        <input type="radio" name="payment_method" id="method_transfer" value="transfer">
                        <label for="method_transfer">
                            🏦 Transfer
                        </label>

                        <input type="radio" name="payment_method" id="method_ota" value="ota">
                        <label for="method_ota">
                            🌐 OTA
                        </label>
                    </div>
                </div>

                {{-- PAYMENT PROOF --}}
                <div class="mb-3">
                    <label class="form-label">Payment Proof (optional)</label>
                    <input type="file" name="payment_proof" id="paymentProofInput" class="form-control bg-dark"
                        accept="image/*" capture="environment" />

                    <small class="text-muted">
                        Optional — you can paste image directly (Ctrl + V)
                    </small>
                </div>

                {{-- IMAGE PREVIEW --}}
                <div class="mt-3">
                    <img id="paymentProofPreview" class="img-fluid rounded d-none shadow" style="max-height: 300px;">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="submitPaymentBtn" data-loading-button>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    <span class="btn-text">Proses Pembayaran</span>
                </button>
            </div>
        </form>
    </div>
</div>
