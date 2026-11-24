@props([
    'href' => '#',
    'title' => 'Tambah Data',
    'icon' => null,
    'desktop' => false,
])

<div class="floating-button creation-add {{ $desktop ? '' : 'd-md-none' }}">
    <a href="{{ $href }}" class="button" title="{{ $title }}" {{ $attributes }}>
        @if($icon)
            <span class="material-symbols-outlined" style="font-size: 24px; color: white;">{{ $icon }}</span>
        @else
            +
        @endif
    </a>
</div>
