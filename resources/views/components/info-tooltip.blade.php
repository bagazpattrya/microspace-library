@props([
    'title' => null,
    'icon' => 'bi-info-circle',
    'trigger' => '!',
    'triggerTitle' => 'Klik untuk lebih detail',
    'description' => null,
    'guides' => null,
    'guideChapter' => null,
    'guideKeyword' => null,
    'maxGuides' => 3,
    'width' => '290px',
    'placement' => 'right',
    'id' => null,
])

@php
    $componentId = $id ?? ('ms_tooltip_' . \Illuminate\Support\Str::random(8));
    
    // Resolve guides collection if provided or queryable
    $resolvedGuides = collect();
    if (!is_null($guides)) {
        $rawGuides = is_array($guides) ? $guides : collect($guides)->toArray();
        $resolvedGuides = collect($rawGuides)->map(function ($item) {
            if (is_object($item)) {
                return $item;
            }
            if (is_array($item)) {
                return (object) $item;
            }
            if (is_string($item) && class_exists(\App\Models\Microspace\UserGuide::class)) {
                $found = \App\Models\Microspace\UserGuide::where('slug', $item)
                    ->orWhere('title', 'like', $item . '%')
                    ->first(['id', 'title', 'slug']);
                if ($found) {
                    return $found;
                }
                return (object) ['title' => $item, 'url' => '#'];
            }
            if (is_numeric($item) && class_exists(\App\Models\Microspace\UserGuide::class)) {
                $found = \App\Models\Microspace\UserGuide::find($item, ['id', 'title', 'slug']);
                if ($found) {
                    return $found;
                }
            }
            return (object) ['title' => (string) $item, 'url' => '#'];
        })->filter();
    } elseif (!empty($guideChapter)) {
        if (class_exists(\App\Models\Microspace\UserGuide::class)) {
            $resolvedGuides = \App\Models\Microspace\UserGuide::where('title', 'like', $guideChapter . '%')
                ->take($maxGuides)
                ->get(['id', 'title', 'slug']);
        }
    } elseif (!empty($guideKeyword)) {
        if (class_exists(\App\Models\Microspace\UserGuide::class)) {
            $resolvedGuides = \App\Models\Microspace\UserGuide::where(function($q) use ($guideKeyword) {
                $q->where('slug', 'like', '%' . $guideKeyword . '%')
                  ->orWhere('title', 'like', '%' . $guideKeyword . '%');
            })->take($maxGuides)->get(['id', 'title', 'slug']);
        }
    }
@endphp

@once('microspace_info_tooltip_styles')
<style>
    /* ==========================================================================
       Microspace Global Reusable Info Tooltip Component
       ========================================================================== */
    .microspace-tooltip-wrapper {
        position: relative;
        display: inline-block;
        vertical-align: middle;
    }

    .microspace-tooltip-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 19px;
        height: 19px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 11px;
        font-weight: 700;
        user-select: none;
        vertical-align: middle;
        background-color: rgba(139, 209, 58, 0.18) !important;
        color: #3b6d11 !important;
        border: 1.5px solid rgba(139, 209, 58, 0.5) !important;
        transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease !important;
    }

    [data-bs-theme="dark"] .microspace-tooltip-trigger {
        background-color: rgba(139, 209, 58, 0.15) !important;
        color: #8bd13a !important;
        border-color: rgba(139, 209, 58, 0.45) !important;
    }

    .microspace-tooltip-trigger:hover,
    .microspace-tooltip-trigger.is-active {
        transform: rotate(360deg) scale(1.22) !important;
        background-color: var(--primary-color, #8bd13a) !important;
        color: #1a2e05 !important;
        border-color: var(--primary-color, #8bd13a) !important;
        box-shadow: none !important;
    }

    @keyframes microspaceTooltipExplode {
        0% { transform: translateY(-75%) scale(0.85); opacity: 0; }
        60% { transform: translateY(-75%) scale(1.03); opacity: 1; }
        100% { transform: translateY(-75%) scale(1); opacity: 1; }
    }

    .microspace-tooltip-bubble {
        display: none;
        position: absolute;
        top: 50%;
        left: calc(100% + 10px);
        transform: translateY(-75%);
        width: var(--tooltip-custom-width, 290px);
        max-width: min(var(--tooltip-custom-width, 290px), calc(100vw - 40px));
        border-radius: 10px;
        padding: 9px 12px;
        z-index: 1060;
        transform-origin: left 75%;
        text-align: left;
    }

    /* Light Theme Bubble (Default) */
    .microspace-tooltip-bubble,
    [data-bs-theme="light"] .microspace-tooltip-bubble {
        background-color: #ffffff !important;
        color: #1e293b !important;
        border: 1px solid rgba(139, 209, 58, 0.45) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1) !important;
    }

    .microspace-tooltip-bubble::after,
    [data-bs-theme="light"] .microspace-tooltip-bubble::after {
        content: '';
        position: absolute;
        top: 75%;
        left: -5px;
        transform: translateY(-50%) rotate(45deg);
        width: 9px;
        height: 9px;
        background-color: #ffffff !important;
        border-left: 1px solid rgba(139, 209, 58, 0.45) !important;
        border-bottom: 1px solid rgba(139, 209, 58, 0.45) !important;
    }

    .microspace-tooltip-bubble .tooltip-title {
        color: #0f172a !important;
        font-size: 0.8125rem;
        font-weight: 700;
    }

    .microspace-tooltip-bubble .tooltip-icon {
        color: #4d8b1e !important;
        font-size: 0.9rem;
    }

    .microspace-tooltip-bubble .tooltip-close-btn {
        filter: none !important;
        opacity: 0.55;
    }
    .microspace-tooltip-bubble .tooltip-close-btn:hover {
        opacity: 1;
    }

    .microspace-tooltip-bubble .tooltip-body-text {
        font-size: 0.74rem;
        line-height: 1.45;
        color: #475569 !important;
        margin-bottom: 0.5rem;
    }
    .microspace-tooltip-bubble .tooltip-body-text p {
        margin-bottom: 0.35rem;
    }
    .microspace-tooltip-bubble .tooltip-body-text p:last-child {
        margin-bottom: 0;
    }

    .microspace-tooltip-bubble .tooltip-highlight {
        color: #365314 !important;
        font-weight: 700;
    }

    .microspace-tooltip-bubble .tooltip-footer {
        border-top: 1px solid #e2e8f0 !important;
        font-size: 0.72rem;
        padding-top: 6px;
    }

    .microspace-tooltip-bubble .tooltip-link-guide {
        color: #3f7415 !important;
        font-weight: 500;
        font-size: 0.72rem;
        line-height: 1.35;
        padding: 3px 6px;
        border-radius: 4px;
        transition: all 0.15s ease;
    }
    .microspace-tooltip-bubble .tooltip-link-guide:hover {
        color: #27480a !important;
        background-color: rgba(139, 209, 58, 0.12);
    }

    /* Dark Theme Bubble */
    [data-bs-theme="dark"] .microspace-tooltip-bubble {
        background-color: #1a1c18 !important;
        color: #f1f5f1 !important;
        border: 1px solid rgba(139, 209, 58, 0.35) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.55) !important;
    }

    [data-bs-theme="dark"] .microspace-tooltip-bubble::after {
        background-color: #1a1c18 !important;
        border-left: 1px solid rgba(139, 209, 58, 0.35) !important;
        border-bottom: 1px solid rgba(139, 209, 58, 0.35) !important;
    }

    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-title {
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-icon {
        color: var(--primary-color, #8bd13a) !important;
    }

    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-close-btn {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
        opacity: 0.7;
    }
    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-close-btn:hover {
        opacity: 1;
    }

    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-body-text {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-highlight {
        color: #8bd13a !important;
        font-weight: 700;
    }

    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.12) !important;
    }

    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-link-guide {
        color: var(--primary-color, #8bd13a) !important;
    }
    [data-bs-theme="dark"] .microspace-tooltip-bubble .tooltip-link-guide:hover {
        color: #b4e67a !important;
        background-color: rgba(139, 209, 58, 0.16);
    }

    .microspace-tooltip-bubble.show {
        display: block;
        animation: microspaceTooltipExplode 0.22s ease-out forwards;
    }

    @media (max-width: 600px) {
        .microspace-tooltip-bubble {
            left: 0;
            top: calc(100% + 8px);
            transform: none;
            transform-origin: 14px top;
        }
        @keyframes microspaceTooltipExplodeMobile {
            0% { transform: scale(0.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .microspace-tooltip-bubble.show {
            animation: microspaceTooltipExplodeMobile 0.18s ease-out forwards;
        }
        .microspace-tooltip-bubble::after,
        [data-bs-theme="light"] .microspace-tooltip-bubble::after,
        [data-bs-theme="dark"] .microspace-tooltip-bubble::after {
            top: -5px;
            bottom: auto;
            left: 12px;
            transform: rotate(45deg);
            border-left: 1px solid rgba(139, 209, 58, 0.45) !important;
            border-top: 1px solid rgba(139, 209, 58, 0.45) !important;
            border-right: none !important;
            border-bottom: none !important;
        }
    }
</style>
@endonce

<span class="microspace-tooltip-wrapper ms-1" id="{{ $componentId }}_wrapper">
    <span class="microspace-tooltip-trigger"
          id="{{ $componentId }}_trigger"
          role="button"
          tabindex="0"
          data-bs-toggle="tooltip"
          data-bs-placement="top"
          title="{{ $triggerTitle }}"
          aria-expanded="false"
          aria-controls="{{ $componentId }}_bubble">{{ $trigger }}</span>

    <div id="{{ $componentId }}_bubble" 
         class="microspace-tooltip-bubble"
         role="tooltip"
         style="--tooltip-custom-width: {{ $width }};">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="tooltip-title d-inline-flex align-items-center">
                @if($icon)
                    <i class="bi {{ $icon }} me-1.5 tooltip-icon"></i>
                @endif
                {{ $title ?? 'Info' }}
            </span>
            <button type="button" class="btn-close tooltip-close-btn p-1" style="font-size: 0.55rem;" aria-label="Close"></button>
        </div>
        
        <div class="tooltip-body-text">
            @if($slot->isNotEmpty())
                {{ $slot }}
            @elseif(!empty($description))
                {!! $description !!}
            @endif
        </div>

        @if($resolvedGuides->isNotEmpty())
            <div class="d-flex flex-column gap-1 pt-1.5 tooltip-footer">
                @foreach($resolvedGuides->take($maxGuides) as $guide)
                    @php
                        $guideTitle = is_object($guide) ? ($guide->title ?? '') : ($guide['title'] ?? '');
                        $guideUrl = '#';
                        if (is_object($guide) && isset($guide->slug)) {
                            $guideUrl = route('user-guides.show', $guide->slug);
                        } elseif (is_array($guide) && isset($guide['slug'])) {
                            $guideUrl = route('user-guides.show', $guide['slug']);
                        } elseif (is_object($guide) && isset($guide->url)) {
                            $guideUrl = $guide->url;
                        } elseif (is_array($guide) && isset($guide['url'])) {
                            $guideUrl = $guide['url'];
                        }
                    @endphp
                    <a href="{{ $guideUrl }}" target="_blank" class="text-decoration-none d-block text-truncate tooltip-link-guide" title="{{ $guideTitle }}">
                        {{ $guideTitle }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</span>

@once('microspace_info_tooltip_scripts')
<script>
(function () {
    if (window._microspaceTooltipInitialized) return;
    window._microspaceTooltipInitialized = true;

    function closeAllMicrospaceTooltips() {
        document.querySelectorAll('.microspace-tooltip-bubble.show').forEach(function (bubble) {
            bubble.classList.remove('show');
        });
        document.querySelectorAll('.microspace-tooltip-trigger.is-active').forEach(function (trig) {
            trig.classList.remove('is-active');
            trig.setAttribute('aria-expanded', 'false');
        });
    }

    function toggleMicrospaceTooltip(wrapper) {
        if (!wrapper) return;
        const trigger = wrapper.querySelector('.microspace-tooltip-trigger');
        const bubble = wrapper.querySelector('.microspace-tooltip-bubble');
        if (!trigger || !bubble) return;

        const isCurrentlyOpen = bubble.classList.contains('show');
        
        // Hide Bootstrap hover tooltip if present
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const bsTooltip = bootstrap.Tooltip.getInstance(trigger);
            if (bsTooltip) bsTooltip.hide();
        }

        // Close all other open tooltips first
        closeAllMicrospaceTooltips();

        if (!isCurrentlyOpen) {
            trigger.classList.add('is-active');
            trigger.setAttribute('aria-expanded', 'true');
            bubble.classList.remove('show');
            void bubble.offsetWidth; // re-trigger animation
            bubble.classList.add('show');
        }
    }

    document.addEventListener('click', function (e) {
        // Trigger clicked
        const trigger = e.target.closest('.microspace-tooltip-trigger');
        if (trigger) {
            e.preventDefault();
            e.stopPropagation();
            toggleMicrospaceTooltip(trigger.closest('.microspace-tooltip-wrapper'));
            return;
        }

        // Close button inside bubble clicked
        const closeBtn = e.target.closest('.tooltip-close-btn');
        if (closeBtn && e.target.closest('.microspace-tooltip-bubble')) {
            e.preventDefault();
            e.stopPropagation();
            closeAllMicrospaceTooltips();
            return;
        }

        // Click inside bubble (e.g. guide links) - do not dismiss immediately unless navigating
        if (e.target.closest('.microspace-tooltip-bubble')) {
            return;
        }

        // Click outside closes all tooltips
        closeAllMicrospaceTooltips();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeAllMicrospaceTooltips();
        }
    });

    function initBootstrapTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            document.querySelectorAll('.microspace-tooltip-trigger:not([data-ms-tooltip-ready])').forEach(function (el) {
                el.setAttribute('data-ms-tooltip-ready', 'true');
                new bootstrap.Tooltip(el);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBootstrapTooltips);
    } else {
        initBootstrapTooltips();
    }
})();
</script>
@endonce
