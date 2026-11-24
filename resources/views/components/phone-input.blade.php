@props([
    'name' => 'phone_number',
    'id' => null,
    'value' => '',
    'label' => 'Nomor Handphone',
    'placeholder' => '812-3456-7890',
    'required' => false,
    'disabled' => false,
    'checkWa' => true,
    'checkUrl' => null,
    'isWhatsapp' => null,
])

@php
    $inputId = $id ?? 'phone_' . substr(md5($name), 0, 8);
    $checkUrl = $checkUrl ?? (Route::has('whatsapp.check-number') ? route('whatsapp.check-number') : '/api/whatsapp/check-number');

    $rawDigits = preg_replace('/\D+/', '', (string) old($name, $value));
    if (str_starts_with($rawDigits, '62')) {
        $rawDigits = substr($rawDigits, 2);
    } elseif (str_starts_with($rawDigits, '0')) {
        $rawDigits = substr($rawDigits, 1);
    }

    $formattedDisplay = $rawDigits;
    if (strlen($rawDigits) >= 4) {
        $formattedDisplay = substr($rawDigits, 0, 3) . '-' . substr($rawDigits, 3, 4) . (strlen($rawDigits) > 7 ? '-' . substr($rawDigits, 7, 6) : '');
    }
@endphp

<div {{ $attributes->merge(['class' => 'ms-phone-field']) }} data-phone-checker data-check-url="{{ $checkUrl }}" data-check-wa="{{ $checkWa ? '1' : '0' }}">
    @if($label)
        <label for="{{ $inputId }}" class="ms-phone-label">
            <svg class="ms-phone-label-icon" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
            </svg>
            <span>{{ $label }}</span>
        </label>
    @endif

    <div class="ms-phone-group">
        <span class="ms-phone-addon">+62</span>
        <input 
            type="tel"
            id="{{ $inputId }}"
            class="ms-phone-input"
            placeholder="{{ $placeholder }}"
            value="{{ $formattedDisplay }}"
            autocomplete="tel-national"
            maxlength="16"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
        >
        <span class="ms-phone-status-icon" aria-hidden="true"></span>
    </div>

    {{-- Hidden canonical values for form submission --}}
    <input type="hidden" name="{{ $name }}" class="ms-phone-hidden-raw" value="{{ $rawDigits ? '62' . $rawDigits : '' }}">
    <input type="hidden" name="is_whatsapp" class="ms-phone-hidden-wa" value="{{ old('is_whatsapp', $isWhatsapp !== null ? ($isWhatsapp ? '1' : '0') : '') }}">

    <div class="ms-phone-feedback" aria-live="polite"></div>
</div>

@once
<style>
/* Microspace Universal Phone Input & WA Checker Component */
.ms-phone-field {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    margin-bottom: 1rem;
    width: 100%;
}

.ms-phone-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--bs-body-color, #e2e8f0);
    margin: 0;
}

.ms-phone-label-icon {
    width: 0.95rem;
    height: 0.95rem;
    opacity: 0.85;
}

.ms-phone-group {
    display: flex;
    align-items: stretch;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.25);
    border-radius: 10px;
    overflow: hidden;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

[data-bs-theme="light"] .ms-phone-group {
    background: #ffffff;
    border-color: #cbd5e1;
}

.ms-phone-group:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.ms-phone-addon {
    display: flex;
    align-items: center;
    padding: 0.55rem 0.85rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--bs-secondary-color, #94a3b8);
    background: rgba(148, 163, 184, 0.08);
    border-right: 1px solid rgba(148, 163, 184, 0.2);
    user-select: none;
}

.ms-phone-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    padding: 0.55rem 0.85rem;
    font-size: 0.95rem;
    color: var(--bs-body-color, #f8fafc);
    letter-spacing: 0.02em;
    min-width: 0;
}

[data-bs-theme="light"] .ms-phone-input {
    color: #0f172a;
}

.ms-phone-input::placeholder {
    color: var(--bs-secondary-color, #64748b);
    opacity: 0.7;
}

.ms-phone-status-icon {
    display: flex;
    align-items: center;
    padding-right: 0.75rem;
    font-size: 0.85rem;
}

.ms-phone-feedback {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8rem;
    font-weight: 600;
    min-height: 1.1rem;
    margin-top: 0.1rem;
}

.ms-phone-feedback:empty {
    display: none;
}

.ms-phone-feedback.is-checking {
    color: var(--bs-secondary-color, #94a3b8);
}

.ms-phone-feedback.is-valid {
    color: #10b981;
}

.ms-phone-feedback.is-invalid {
    color: #ef4444;
}

.ms-phone-feedback.is-warn {
    color: #f59e0b;
}
</style>

<script>
(function() {
    if (window._msPhoneCheckerInitialized) return;
    window._msPhoneCheckerInitialized = true;

    function formatNumber(digits) {
        if (!digits) return '';
        if (digits.length <= 3) return digits;
        if (digits.length <= 7) return digits.slice(0, 3) + '-' + digits.slice(3);
        return digits.slice(0, 3) + '-' + digits.slice(3, 7) + '-' + digits.slice(7, 13);
    }

    function initPhoneField(wrapper) {
        const input = wrapper.querySelector('.ms-phone-input');
        const hiddenRaw = wrapper.querySelector('.ms-phone-hidden-raw');
        const hiddenWa = wrapper.querySelector('.ms-phone-hidden-wa');
        const feedback = wrapper.querySelector('.ms-phone-feedback');
        const checkUrl = wrapper.dataset.checkUrl;
        const checkWa = wrapper.dataset.checkWa === '1';

        let timer = null;
        let lastChecked = '';

        function setFeedback(type, text) {
            feedback.className = 'ms-phone-feedback is-' + type;
            feedback.textContent = text;
        }

        function clearFeedback() {
            feedback.className = 'ms-phone-feedback';
            feedback.textContent = '';
        }

        function check(phoneDigits) {
            if (!checkWa) return;
            const fullE164 = '62' + phoneDigits;

            if (phoneDigits.length < 8) {
                hiddenWa.value = '0';
                setFeedback('warn', '⚠️ Format nomor terlalu pendek (min. 8 digit)');
                return;
            }

            if (lastChecked === fullE164) return;
            lastChecked = fullE164;

            setFeedback('checking', '⏳ Mengecek WhatsApp...');

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            fetch(checkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ number: fullE164 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.exists) {
                    hiddenWa.value = '1';
                    setFeedback('valid', '✅ Nomor terdaftar di WhatsApp');
                } else if (data.success && !data.exists) {
                    hiddenWa.value = '0';
                    setFeedback('invalid', '⚠️ Nomor tidak terdaftar di WhatsApp');
                } else {
                    hiddenWa.value = '0';
                    setFeedback('warn', '⚠️ ' + (data.message || 'Gagal verifikasi WhatsApp'));
                }
            })
            .catch(() => {
                setFeedback('warn', '⚠️ Tidak dapat terhubung ke validator WhatsApp');
            });
        }

        input.addEventListener('input', function() {
            let digits = input.value.replace(/\D/g, '');
            if (digits.startsWith('62')) digits = digits.slice(2);
            else if (digits.startsWith('0')) digits = digits.slice(1);

            input.value = formatNumber(digits);
            hiddenRaw.value = digits ? '62' + digits : '';

            clearTimeout(timer);

            if (!digits) {
                hiddenWa.value = '';
                lastChecked = '';
                clearFeedback();
                return;
            }

            if (digits.length >= 8) {
                timer = setTimeout(() => check(digits), 600);
            } else {
                clearFeedback();
            }
        });

        input.addEventListener('blur', function() {
            let digits = input.value.replace(/\D/g, '');
            if (digits.startsWith('62')) digits = digits.slice(2);
            else if (digits.startsWith('0')) digits = digits.slice(1);

            if (digits && digits.length < 8) {
                setFeedback('warn', '⚠️ Format nomor terlalu pendek');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-phone-checker]').forEach(initPhoneField);
    });

    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        document.querySelectorAll('[data-phone-checker]').forEach(initPhoneField);
    }
})();
</script>
@endonce
