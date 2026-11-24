{{--
    Reusable side drawer (Bootstrap offcanvas, slides in from the right; becomes a bottom sheet on phones).

    Props
      id        required  element id, used by the trigger: data-bs-toggle="offcanvas" data-bs-target="#{id}"
      title     header title
      icon      bootstrap-icons class shown beside the title, e.g. "bi-patch-check-fill" (optional)
      width     desktop width, any CSS length (default 460px); phones (<576px) always get a full-width bottom sheet
      form      true wraps header/body/footer in a <form id="{id}Form">; use action/method to configure it
      action    form action (can also be set from JS: form.action = ...)
      method    form method (default POST, @csrf is added automatically)

    Slots
      default   drawer body (scrollable)
      footer    sticky action bar at the bottom (optional)
      header    extra content on the right of the title, before the close button (optional)

    Example
      <button data-bs-toggle="offcanvas" data-bs-target="#detailDrawer">Open</button>

      <x-side-drawer id="detailDrawer" title="Detail" icon="bi-info-circle">
          ...body...
          <x-slot:footer><button class="btn btn-primary w-100">Simpan</button></x-slot:footer>
      </x-side-drawer>

    JS
      const drawer = bootstrap.Offcanvas.getOrCreateInstance('#detailDrawer'); drawer.show() / drawer.hide();
      The element fires the standard show.bs.offcanvas / hidden.bs.offcanvas events; e.relatedTarget is the
      trigger button, handy for reading its data-* attributes.
--}}
@props([
    'id',
    'title' => '',
    'icon' => null,
    'width' => '460px',
    'form' => false,
    'action' => '',
    'method' => 'POST',
])

@once
    @push('styles')
        <style>
            /* Own full-screen scrim instead of Bootstrap's backdrop: the app layout has elements with z-index up to
               999999 that painted above the stock backdrop, so only part of the page got dimmed. */
            .side-drawer {
                --bs-offcanvas-zindex: 1000002;
            }

            .side-drawer-scrim {
                position: fixed;
                inset: 0;
                z-index: 1000001;
                background: rgba(0, 0, 0, 0.5);
                opacity: 0;
                transition: opacity 0.25s ease;
            }

            .side-drawer-scrim.show {
                opacity: 1;
            }

            .side-drawer {
                --bs-offcanvas-width: var(--side-drawer-width, 460px);
                --bs-offcanvas-border-color: var(--bs-border-color);
            }

            /* Pre-promote the panel to its own layer so the first frame of the slide doesn't wait on a repaint. */
            .side-drawer {
                will-change: transform;
            }

            /* Cartoon-style stop: open overshoots past the resting spot then settles back;
               close winds up slightly (anticipation) before snapping away. */
            .side-drawer.showing {
                transition: transform 0.38s cubic-bezier(0.3, 1.6, 0.5, 1);
            }

            .side-drawer.hiding {
                transition: transform 0.22s cubic-bezier(0.55, -0.35, 0.8, 0.5);
            }

            /* Covers the gap that opens beside the panel while it overshoots. */
            .side-drawer.offcanvas-end::after {
                content: '';
                position: absolute;
                top: 0;
                bottom: 0;
                left: 100%;
                width: 120px;
                background: var(--bs-offcanvas-bg);
            }

            @media (prefers-reduced-motion: reduce) {
                body .side-drawer.offcanvas-end.showing,
                body .side-drawer.offcanvas-end.hiding {
                    transition: none !important;
                }
            }

            .side-drawer .offcanvas-header {
                padding: 1rem 1.25rem 0.5rem;
            }

            .side-drawer .offcanvas-body {
                padding: 0.5rem 1.25rem;
            }

            .side-drawer-icon {
                width: 32px;
                height: 32px;
                background: rgba(212, 10, 112, 0.15);
                color: var(--primary-color-lighter, #f05fa3);
                font-size: 1.1rem;
            }

            .side-drawer-footer {
                display: flex;
                gap: 0.5rem;
                padding: 0.75rem 1.25rem max(1rem, env(safe-area-inset-bottom));
                border-top: 1px solid var(--bs-border-color);
            }

            .side-drawer-form {
                display: flex;
                flex-direction: column;
                height: 100%;
                min-height: 0;
            }

            /* Full-width grab zone; the visible bar is the ::after. */
            .side-drawer-handle {
                display: none;
                width: 100%;
                padding: 0.7rem 0 0.35rem;
                cursor: grab;
                touch-action: none;
            }

            .side-drawer-handle::after {
                content: '';
                display: block;
                width: 44px;
                height: 5px;
                margin: 0 auto;
                background-color: rgba(128, 128, 128, 0.55);
                border-radius: 3px;
            }

            /* Phones: the drawer becomes a bottom sheet. */
            @media (max-width: 575.98px) {
                .side-drawer.offcanvas-end {
                    top: auto;
                    right: 0;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    /* vh, not dvh: dvh resizes the sheet every time the mobile browser toolbar slides. */
                    height: 60vh;
                    border-left: 0;
                    border-top: 1px solid var(--bs-border-color);
                    border-radius: 1.25rem 1.25rem 0 0;
                    transform: translateY(100%);
                }

                /* Pops up immediately (~75% of the way in the first 10% of the time), overshoots ~8%, then settles.
                   Selectors are deliberately specific so nothing else (Bootstrap, the swipe handler) can override them. */
                .side-drawer.offcanvas-end.showing {
                    transition: transform 0.1s cubic-bezier(0.12, 1.5, 0.3, 1) !important;
                }

                .side-drawer.offcanvas-end.hiding {
                    transition: transform 0.18s cubic-bezier(0.5, -0.4, 0.8, 0.5) !important;
                }

                /* Fills the gap that opens under the sheet while it is stretched upward (rubber-band effect). */
                .side-drawer.offcanvas-end::after {
                    content: '';
                    position: absolute;
                    left: 0;
                    right: 0;
                    top: 100%;
                    bottom: auto;
                    width: auto;
                    height: 120px;
                    background: var(--bs-offcanvas-bg);
                }

                .side-drawer-handle {
                    display: block;
                }

                /* Header is also a drag zone for swipe-to-close. */
                .side-drawer .offcanvas-header {
                    touch-action: none;
                }

                .side-drawer .offcanvas-body {
                    overscroll-behavior: contain;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Re-parent drawers to <body> so an ancestor's stacking context (transform/overflow/z-index)
            // can't trap them beneath the fixed sidebar.
            document.addEventListener('show.bs.offcanvas', (e) => {
                const d = e.target;
                if (d.classList?.contains('side-drawer') && d.parentElement !== document.body) {
                    document.body.appendChild(d);
                }
            });
        </script>
        <script>
            document.addEventListener('show.bs.offcanvas', (e) => {
                const d = e.target;
                if (!d.classList?.contains('side-drawer') || d._scrim) return;
                const scrim = document.createElement('div');
                scrim.className = 'side-drawer-scrim';
                document.body.appendChild(scrim);
                requestAnimationFrame(() => scrim.classList.add('show'));
                d._scrim = scrim;
            });
            document.addEventListener('hide.bs.offcanvas', (e) => {
                const d = e.target, scrim = d._scrim;
                if (!d.classList?.contains('side-drawer') || !scrim) return;
                d._scrim = null;
                scrim.classList.remove('show');
                setTimeout(() => scrim.remove(), 260);
            });
        </script>
        <script>
            // Clicking anywhere outside an open drawer closes it (the page behind it, the sidebar, the backdrop).
            // Ignores clicks inside any drawer/modal/dropdown/datepicker so nested widgets keep working, and
            // clicks that start inside the drawer and end outside (e.g. text selection drags).
            (function () {
                let downInside = false;
                const keep = '.side-drawer, .modal, .dropdown-menu, .flatpickr-calendar, .select2-container, .swal2-container, .tom-select, .ts-dropdown';

                document.addEventListener('mousedown', (e) => {
                    downInside = !!e.target.closest?.(keep);
                });

                document.addEventListener('click', (e) => {
                    if (downInside || e.target.closest?.(keep)) return;
                    // A trigger button opening a drawer must not immediately close it.
                    if (e.target.closest?.('[data-bs-toggle="offcanvas"]')) return;
                    if (!e.target.isConnected) return;
                    document.querySelectorAll('.side-drawer.show').forEach((d) => {
                        bootstrap.Offcanvas.getInstance(d)?.hide();
                    });
                });
            })();
        </script>
        <script>
            // Phones: swipe a bottom sheet down to close it. Works from the handle, the header, the footer, or any
            // non-interactive spot of the body (body only while it is scrolled to the top, so normal scrolling still works).
            (function () {
                const phone = window.matchMedia('(max-width: 575.98px)');
                const interactive = 'input, textarea, select, button, a, label, img, [contenteditable]';
                const STRETCH_MAX = 28;   // px the sheet can visually "grow" when pulled up

                let el = null, bodyEl = null, startY = 0, startT = 0, dy = 0, armed = false, dragging = false, frame = 0;

                function reset() {
                    if (frame) { cancelAnimationFrame(frame); frame = 0; }
                    el = null; bodyEl = null; armed = false; dragging = false; dy = 0;
                }

                // Rubber-band: resistance grows as the finger moves, never exceeding STRETCH_MAX.
                function stretch(up) {
                    return STRETCH_MAX * (1 - Math.exp(-up / 120));
                }

                // The body scrolls instead of stretching unless it has nothing left to scroll up into.
                function canScrollMore(body) {
                    return body && body.scrollHeight > body.clientHeight + 1 &&
                        body.scrollTop + body.clientHeight < body.scrollHeight - 1;
                }

                document.addEventListener('touchstart', function (e) {
                    const sheet = e.target.closest ? e.target.closest('.side-drawer.show') : null;
                    if (!sheet || !phone.matches || e.touches.length !== 1) return reset();

                    const body = sheet.querySelector('.offcanvas-body');
                    const inBody = body && body.contains(e.target);
                    if (inBody && (body.scrollTop > 0 || e.target.closest(interactive))) return reset();
                    if (!inBody && e.target.closest(interactive)) return reset();

                    el = sheet;
                    bodyEl = inBody ? body : null;
                    startY = e.touches[0].clientY;
                    startT = Date.now();
                    armed = true;
                    dragging = false;
                    dy = 0;
                }, { passive: true });

                // Passive on purpose: a non-passive touchmove makes the browser wait for JS on every move and makes
                // scrolling feel sticky. preventDefault is not needed: handle/header are touch-action: none and the
                // body has overscroll-behavior: contain, so a downward pull at scrollTop 0 scrolls nothing.
                document.addEventListener('touchmove', function (e) {
                    if (!armed || !el) return;
                    dy = e.touches[0].clientY - startY;

                    if (!dragging) {
                        if (Math.abs(dy) <= 6) return;
                        if (dy < 0 && canScrollMore(bodyEl)) { armed = false; return; }   // swiping up = normal scroll
                        dragging = true;
                        el.style.transition = 'none';
                        el.style.willChange = 'transform';
                    }

                    if (!frame) {
                        frame = requestAnimationFrame(function () {
                            frame = 0;
                            if (!el || !dragging) return;
                            const y = dy >= 0 ? dy : -stretch(-dy);
                            el.style.transform = 'translate3d(0, ' + y + 'px, 0)';
                        });
                    }
                }, { passive: true });

                function finish() {
                    if (!el) return;
                    const sheet = el;
                    const wasDragging = dragging;
                    const distance = Math.max(0, dy);
                    const pulledUp = dy < 0;
                    const speed = distance / Math.max(1, Date.now() - startT);
                    reset();
                    if (!wasDragging) return;

                    // Hand the animation back to CSS: either snap back (with a small bounce after a pull up)
                    // or slide out from the current position.
                    sheet.style.transition = pulledUp ? 'transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1)' : '';
                    sheet.style.transform = '';
                    sheet.style.willChange = '';
                    if (pulledUp) setTimeout(function () { sheet.style.transition = ''; }, 480);
                    if (distance > sheet.offsetHeight * 0.25 || speed > 0.5) {
                        bootstrap.Offcanvas.getInstance(sheet)?.hide();
                    }
                }

                document.addEventListener('touchend', finish);
                document.addEventListener('touchcancel', finish);
            })();
        </script>
    @endpush
@endonce

<div {{ $attributes
        ->merge(['data-bs-backdrop' => 'false'])
        ->class(['offcanvas', 'offcanvas-end', 'side-drawer'])
        ->style(["--side-drawer-width: {$width}"]) }}
    id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label">

    @if ($form)
        <form id="{{ $id }}Form" method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}"
            class="side-drawer-form">
            @csrf
            @if (! in_array(strtoupper($method), ['GET', 'POST']))
                @method($method)
            @endif
    @else
        <div class="side-drawer-form">
    @endif

        <div class="side-drawer-handle"></div>

        <div class="offcanvas-header justify-content-between border-bottom-0">
            <div class="d-flex align-items-center gap-2">
                @if ($icon)
                    <span class="side-drawer-icon d-inline-flex align-items-center justify-content-center rounded-circle">
                        <i class="bi {{ $icon }}"></i>
                    </span>
                @endif
                <h5 class="offcanvas-title fw-bold mb-0" id="{{ $id }}Label">{{ $title }}</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                {{ $header ?? '' }}
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
            </div>
        </div>

        <div class="offcanvas-body">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="side-drawer-footer">
                {{ $footer }}
            </div>
        @endisset

    @if ($form)
        </form>
    @else
        </div>
    @endif
</div>
