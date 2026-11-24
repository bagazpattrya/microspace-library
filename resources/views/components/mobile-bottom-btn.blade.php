<div class="d-md-none fixed-bottom pb-3 pt-2 px-0 d-flex justify-content-center" style="z-index: 1040; bottom: calc(8vh + 10px + env(safe-area-inset-bottom, 0px)); pointer-events: none;">
    <div style="width: 90%; max-width: 90%; pointer-events: auto;">
        <button type="{{ $type ?? 'button' }}" 
                {{ $attributes->merge(['class' => 'btn btn-primary w-100 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-lg rounded-3']) }}>
            @if(isset($icon))
                <span class="material-symbols-outlined">{{ $icon }}</span>
            @endif
            {{ $slot }}
        </button>
    </div>
</div>
<div style="height: 120px;" class="d-md-none"></div>
