@props([
    'id' => 'remember',
    'name' => 'remember',
    'value' => '1',
    'label' => 'Ingat Saya',
    'checked' => null,
    'disabled' => false,
    'variant' => 'checkbox', // 'checkbox' | 'switch'
    'forgotUrl' => null,
    'forgotText' => 'Lupa Password?',
])

@php
    $isChecked = $checked !== null
        ? (bool) $checked
        : (old($name) !== null ? (bool) old($name) : (bool) request()->cookie('remembered_identifier'));
@endphp

<div {{ $attributes->merge(['class' => 'remember-me-wrapper' . ($forgotUrl ? ' has-forgot' : '')]) }}>
    <label for="{{ $id }}" class="remember-me-label-box {{ $disabled ? 'is-disabled' : '' }}" title="{{ $label }}">
        <input 
            type="checkbox" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ $value }}" 
            class="remember-me-native-input"
            {{ $isChecked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
        >

        @if($variant === 'switch')
            <span class="remember-me-switch-track" aria-hidden="true">
                <span class="remember-me-switch-thumb"></span>
            </span>
        @else
            <span class="remember-me-custom-check" aria-hidden="true">
                <svg class="remember-me-svg-icon" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.5 7.2L5.5 10.2L11.5 3.8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        @endif

        @if($label)
            <span class="remember-me-text">{{ $label }}</span>
        @endif
    </label>

    @if($forgotUrl)
        <div class="remember-me-forgot-link">
            <a href="{{ $forgotUrl }}">{{ $forgotText }}</a>
        </div>
    @endif
</div>

@once
<style>
/* ========================================================
   REUSABLE REMEMBER ME COMPONENT STYLES
   ======================================================== */
.remember-me-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: flex-start;
    user-select: none;
}

.remember-me-wrapper.has-forgot {
    width: 100%;
    justify-content: space-between;
    gap: 12px;
}

.remember-me-label-box {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    cursor: pointer;
    margin: 0;
    padding: 2px 0;
    transition: opacity 0.2s ease, transform 0.15s ease;
}

.remember-me-label-box:hover:not(.is-disabled) {
    opacity: 0.92;
}

.remember-me-label-box:active:not(.is-disabled) {
    transform: scale(0.98);
}

.remember-me-label-box.is-disabled {
    cursor: not-allowed;
    opacity: 0.55;
}

/* Hidden native input with accessible focus preserved */
.remember-me-native-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    margin: 0;
    padding: 0;
    pointer-events: none;
}

/* Custom Checkbox Design */
.remember-me-custom-check {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    background: rgba(255, 255, 255, 0.08);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    box-sizing: border-box;
    backdrop-filter: blur(4px);
}

/* Light background fallback check border */
body.light-theme .remember-me-custom-check,
.light-mode .remember-me-custom-check {
    border-color: rgba(0, 0, 0, 0.25);
    background: rgba(0, 0, 0, 0.04);
}

/* Checkbox SVG icon */
.remember-me-svg-icon {
    width: 11px;
    height: 11px;
    color: #ffffff;
    stroke-dasharray: 18;
    stroke-dashoffset: 18;
    transition: stroke-dashoffset 0.22s ease-out;
}

/* Checkbox Active / Checked State */
.remember-me-native-input:checked + .remember-me-custom-check {
    background: var(--accent, var(--golden-solid, #3b82f6));
    border-color: var(--accent, var(--golden-solid, #3b82f6));
    box-shadow: 0 2px 8px rgba(var(--accent-rgb, 212, 10, 112), 0.35);
}

.remember-me-native-input:checked + .remember-me-custom-check .remember-me-svg-icon {
    stroke-dashoffset: 0;
}

/* Focus Visible Accessibility */
.remember-me-native-input:focus-visible + .remember-me-custom-check,
.remember-me-native-input:focus-visible + .remember-me-switch-track {
    outline: 2px solid var(--accent, var(--golden-solid, #3b82f6));
    outline-offset: 2px;
}

/* Switch Variant Track & Thumb */
.remember-me-switch-track {
    width: 36px;
    height: 20px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.2);
    position: relative;
    display: inline-block;
    flex-shrink: 0;
    transition: background-color 0.25s ease;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.remember-me-switch-thumb {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #ffffff;
    position: absolute;
    top: 2px;
    left: 2px;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.remember-me-native-input:checked + .remember-me-switch-track {
    background: var(--accent, var(--golden-solid, #3b82f6));
    border-color: var(--accent, var(--golden-solid, #3b82f6));
}

.remember-me-native-input:checked + .remember-me-switch-track .remember-me-switch-thumb {
    transform: translateX(16px);
}

/* Text Label */
.remember-me-text {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--main-text, inherit);
    line-height: 1;
    letter-spacing: 0.01em;
}

/* Forgot Password Link inside wrapper */
.remember-me-forgot-link a {
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--golden-solid, var(--accent, #3b82f6));
    transition: opacity 0.2s ease;
}

.remember-me-forgot-link a:hover {
    opacity: 0.8;
}
</style>
@endonce
