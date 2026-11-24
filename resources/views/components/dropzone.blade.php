@props([
    'id',
    'name',
    'label',
    'accept' => 'image/*,application/pdf',
    'required' => false,
    'icon' => 'upload_file',
    'iconColor' => 'text-secondary',
    'placeholder' => 'Tarik file ke sini atau klik untuk memilih',
    'col' => 'col-md-12'
])

<div class="{{ $col }} mb-3">
    <label class="form-label small text-muted mb-1 fw-bold">
        {{ $label }} 
        @if($required) <span class="text-danger">*</span> @endif
    </label>
    
    <div {{ $attributes->merge([
        'class' => 'dropzone rounded-3 p-3 custom-dropzone-box',
        'id' => $id
    ]) }} 
    data-name="{{ $name }}"
    data-accept="{{ $accept }}">
        <div class="dz-message needsclick my-0">
            <span class="material-symbols-outlined {{ $iconColor }} fs-1">{{ $icon }}</span>
            <p class="mb-0 mt-2 small text-muted">{!! $placeholder !!}</p>
        </div>
    </div>
</div>