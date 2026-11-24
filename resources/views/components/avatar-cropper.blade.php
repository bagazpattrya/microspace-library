@props([
    'currentPhoto' => null,
    'uploadUrl' => '',
    'userId' => null,
    'fieldName' => 'cropped_image',
    'formId' => 'form_photo_cropper',
    'isNested' => true,
])

@once
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.min.js" defer></script>
@endonce

<style>
    .ps-avatar-card {
        display: flex;
        align-items: center;
        gap: 16px;
        background: var(--card-bg, rgba(255, 255, 255, 0.05));
        border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
        border-radius: 14px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        color: inherit;
    }

    .ps-avatar-card:hover {
        background: var(--card-hover-bg, rgba(255, 255, 255, 0.08));
        border-color: var(--border-hover, rgba(255, 255, 255, 0.18));
    }

    .ps-avatar-ring {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid var(--border-color, rgba(255, 255, 255, 0.12));
        overflow: hidden;
        flex-shrink: 0;
        background: var(--avatar-bg, rgba(255, 255, 255, 0.08));
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ps-avatar-ring img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ps-avatar-placeholder {
        font-size: 26px;
        opacity: 0.4;
    }

    .ps-avatar-info h3 {
        margin: 0 0 2px;
        font-size: 15px;
        font-weight: 600;
    }

    .ps-avatar-info p {
        margin: 0;
        font-size: 13px;
        opacity: 0.5;
    }

    .ps-avatar-chevron {
        margin-left: auto;
        font-size: 18px;
        opacity: 0.35;
        flex-shrink: 0;
    }

    .ps-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.65);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }

    .ps-modal-backdrop.active {
        display: flex;
    }

    .ps-modal {
        background: var(--modal-bg, #1e1e1e);
        border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
        border-radius: 16px;
        width: 90%;
        max-width: 680px;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        color: inherit;
    }

    .ps-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color, rgba(255, 255, 255, 0.08));
    }

    .ps-modal-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .ps-modal-close {
        background: none;
        border: none;
        font-size: 20px;
        opacity: 0.4;
        cursor: pointer;
        padding: 0;
        color: inherit;
    }

    .ps-modal-body {
        padding: 1.25rem;
        overflow: auto;
        flex: 1;
    }

    .ps-modal-body .img-container img {
        display: block;
        max-width: 100%;
        max-height: 350px;
        margin: 0 auto;
    }

    .ps-crop-preview-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .ps-crop-preview-label {
        font-size: 12px;
        opacity: 0.45;
        font-weight: 500;
    }

    .ps-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--border-color, rgba(255, 255, 255, 0.12));
    }

    .ps-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border-color, rgba(255, 255, 255, 0.08));
    }

    .ps-btn {
        padding: 9px 20px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: background 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .ps-btn-cancel {
        background: transparent;
        border-color: var(--border-color, rgba(255, 255, 255, 0.12));
        color: inherit;
        opacity: 0.7;
    }

    .ps-btn-cancel:hover {
        opacity: 1;
        color: inherit;
    }

    .ps-btn-save {
        background: var(--border-hover, rgba(255, 255, 255, 0.12));
        border-color: var(--border-hover, rgba(255, 255, 255, 0.18));
        color: inherit;
    }

    .ps-btn-save:hover {
        background: var(--card-hover-bg, rgba(255, 255, 255, 0.18));
    }
</style>

<div class="ps-avatar-card" id="avatar-trigger-card">
    <div class="ps-avatar-ring">
        @if ($currentPhoto)
            <img src="{{ $currentPhoto }}" alt="Foto Profil">
        @else
            <span class="ps-avatar-placeholder"><i class="bi bi-person-fill"></i></span>
        @endif
    </div>
    <div class="ps-avatar-info">
        <h3>Ganti Foto Profil</h3>
        <p>JPG atau PNG &bull; Maks. 2MB &bull; Akan dipotong otomatis</p>
    </div>
    <i class="bi bi-chevron-right ps-avatar-chevron"></i>
</div>

<input type="file" id="avatar-file-input" accept="image/*" style="display:none">

@if ($isNested)
    <input type="hidden" name="{{ $fieldName }}" id="cropped-base64-data">
@else
    <form action="{{ $uploadUrl }}" id="{{ $formId }}" method="POST">
        @csrf
        @if ($userId)
            <input type="hidden" name="user_id" value="{{ $userId }}">
        @endif
        <input type="hidden" name="{{ $fieldName }}" id="cropped-base64-data">
    </form>
@endif

<div class="ps-modal-backdrop" id="avatarCropModal">
    <div class="ps-modal">
        <div class="ps-modal-header">
            <h5>Sesuaikan Foto Profil</h5>
            <button class="ps-modal-close" id="avatar-modal-close" type="button">&times;</button>
        </div>
        <div class="ps-modal-body">
            <div class="row align-items-center">
                <div class="col-md-8 mb-3 mb-md-0">
                    <div class="img-container">
                        <img id="avatar-crop-image" src="" alt="Crop preview">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ps-crop-preview-wrap">
                        <div class="ps-crop-preview-label">Preview Bulat</div>
                        <div class="ps-preview" id="avatar-crop-preview"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ps-modal-footer">
            <button type="button" class="ps-btn ps-btn-cancel" id="avatar-modal-cancel">Batal</button>
            <button type="button" class="ps-btn ps-btn-save" id="avatar-submit-crop-btn">
                <i class="bi bi-scissors"></i> Potong & Simpan
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const triggerCard = document.getElementById('avatar-trigger-card');
        const fileInput = document.getElementById('avatar-file-input');
        const modal = document.getElementById('avatarCropModal');
        const cropImage = document.getElementById('avatar-crop-image');
        const submitBtn = document.getElementById('avatar-submit-crop-btn');
        const closeBtn = document.getElementById('avatar-modal-close');
        const cancelBtn = document.getElementById('avatar-modal-cancel');
        const hiddenDataInput = document.getElementById('cropped-base64-data');
        let cropperIns = null;

        if (!triggerCard) return;

        triggerCard.addEventListener('click', () => fileInput.click());

        function openCropModal() {
            modal.classList.add('active');
        }

        function closeCropModal() {
            modal.classList.remove('active');
            if (cropperIns) {
                cropperIns.destroy();
                cropperIns = null;
            }
            fileInput.value = '';
        }

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            cropImage.src = URL.createObjectURL(file);
            openCropModal();

            cropImage.onload = function() {
                if (cropperIns) cropperIns.destroy();
                cropperIns = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 2,
                    preview: '#avatar-crop-preview',
                });
            };
        });

        submitBtn.addEventListener('click', function() {
            if (!cropperIns) return;

            const canvas = cropperIns.getCroppedCanvas({
                width: 240,
                height: 240
            });
            const base64Data = canvas.toDataURL('image/jpeg');

            hiddenDataInput.value = base64Data;

            const avatarImage = triggerCard.querySelector('img');
            if (avatarImage) {
                avatarImage.src = base64Data;
            } else {
                triggerCard.querySelector('.ps-avatar-ring').innerHTML =
                    `<img src="${base64Data}" alt="Foto Profil">`;
            }
            closeCropModal();
        });

        closeBtn.addEventListener('click', closeCropModal);
        cancelBtn.addEventListener('click', closeCropModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeCropModal();
        });
    });
</script>
