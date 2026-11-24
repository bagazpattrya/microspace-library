// microspace-v1.0.9a

// Safely check for Dropzone before configuring
if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;
}

document.addEventListener('DOMContentLoaded', function () {
    // --- Sidebar Toggle & Dynamic Responsive Logic ---
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
    const sidebarCollapseIcon = document.getElementById('sidebarCollapseIcon');
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    const mainWrapper = document.querySelector('.main-wrapper');

    if (sidebarCollapseBtn && sidebarWrapper && mainWrapper) {
        let isAutoCollapsed = false;

        function updateSidebarIcon() {
            if (!sidebarCollapseIcon) return;
            const isCollapsed = sidebarWrapper.classList.contains('sidebar-collapsed');
            sidebarCollapseIcon.classList.toggle('bi-chevron-left', !isCollapsed);
            sidebarCollapseIcon.classList.toggle('bi-chevron-right', isCollapsed);
        }

        function applySidebarResponsiveState() {
            const width = window.innerWidth;

            if (width <= 801) {
                // Mobile View: sidebar is hidden by .big-screen
                sidebarWrapper.classList.remove('sidebar-collapsed');
                mainWrapper.classList.remove('sidebar-collapsed');
                isAutoCollapsed = false;
            } else if (width <= 960) {
                // Screen is narrow / no longer feasible to keep 250px full sidebar without cramming content
                const savedPreference = localStorage.getItem('sidebar_collapsed');
                if (savedPreference === 'false') {
                    sidebarWrapper.classList.remove('sidebar-collapsed');
                    mainWrapper.classList.remove('sidebar-collapsed');
                    isAutoCollapsed = false;
                } else {
                    sidebarWrapper.classList.add('sidebar-collapsed');
                    mainWrapper.classList.add('sidebar-collapsed');
                    if (savedPreference !== 'true') {
                        isAutoCollapsed = true;
                    }
                }
            } else {
                // Screen is wide enough to comfortably fit full sidebar (> 960px)
                if (isAutoCollapsed) {
                    sidebarWrapper.classList.remove('sidebar-collapsed');
                    mainWrapper.classList.remove('sidebar-collapsed');
                    isAutoCollapsed = false;
                } else {
                    // Respect explicit user manual collapse
                    const isManualCollapsed = localStorage.getItem('sidebar_collapsed') === 'true' || document.cookie.indexOf('sidebar_collapsed=true') !== -1;
                    sidebarWrapper.classList.toggle('sidebar-collapsed', isManualCollapsed);
                    mainWrapper.classList.toggle('sidebar-collapsed', isManualCollapsed);
                }
            }
            updateSidebarIcon();
        }

        // Apply on initial load
        applySidebarResponsiveState();

        // Auto collapse / expand dynamically on window resize
        window.addEventListener('resize', applySidebarResponsiveState);

        // Manual toggle button click
        sidebarCollapseBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const willBeCollapsed = !sidebarWrapper.classList.contains('sidebar-collapsed');
            sidebarWrapper.classList.toggle('sidebar-collapsed', willBeCollapsed);
            mainWrapper.classList.toggle('sidebar-collapsed', willBeCollapsed);
            updateSidebarIcon();

            // When user manually toggles, reset auto-collapsed state
            isAutoCollapsed = false;

            // Persist user preference across page navigation via localStorage and Cookie
            localStorage.setItem('sidebar_collapsed', willBeCollapsed ? 'true' : 'false');
            document.cookie = "sidebar_collapsed=" + (willBeCollapsed ? 'true' : 'false') + "; path=/; max-age=31536000; SameSite=Lax";
        });
    }

    // --- Theme Toggle Logic ---
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const themeStylesheet = document.getElementById('theme-stylesheet');
    const htmlEl = document.documentElement;

    function applyTheme(theme) {
        if (!themeStylesheet) return;
        let currentHref = themeStylesheet.getAttribute('href') || '';
        let newHref = theme === 'light'
            ? currentHref.replace('dark-theme.css', 'light-theme.css')
            : currentHref.replace('light-theme.css', 'dark-theme.css');

        themeStylesheet.setAttribute('href', newHref);
        htmlEl.setAttribute('data-bs-theme', theme);

        // Icon swap/spin is handled in CSS via [data-bs-theme]
        localStorage.setItem('marketing_theme', theme);
        localStorage.setItem('kane_theme', theme);
        document.cookie = 'theme=' + theme + '; path=/; max-age=' + (60 * 60 * 24 * 365) + '; SameSite=Lax';
    }

    const currentSavedTheme = localStorage.getItem('kane_theme') || localStorage.getItem('marketing_theme') || 'dark';
    applyTheme(currentSavedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function (e) {
            e.preventDefault();
            const currentTheme = htmlEl.getAttribute('data-bs-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        });
    }

    // --- DOM Elements ---
    const elements = {
        notifTile: document.getElementById('notif-tile'),
        accountMenu: document.getElementById('settingAccount'),
        btnNotif: document.getElementById('toggleNotification'),
        btnAccount: document.getElementById('toggleSettingAccount'),
        fullscreenBtn: document.getElementById('fullscreen-btn'),
        notifPop: document.querySelector('.notification-pop'),
        alertContainer: typeof $ !== 'undefined' ? $('#alertx-container') : []
    };
    let isFullscreen = false;

    function finishNotifClose() {
        elements.notifTile.classList.remove('is-closing');
        elements.notifTile.classList.add('is-hidden');
    }

    function closeNotifTile() {
        const tile = elements.notifTile;
        if (!tile || tile.classList.contains('is-hidden') || tile.classList.contains('is-closing')) return;
        tile.classList.add('is-closing');
        // No animation (e.g. reduced motion) means animationend never fires
        if (getComputedStyle(tile).animationName === 'none') finishNotifClose();
    }

    if (elements.notifTile) {
        elements.notifTile.addEventListener('animationend', (e) => {
            if (e.target === elements.notifTile && e.animationName === 'notif-panel-out') finishNotifClose();
        });
    }

    function finishAccountClose() {
        elements.accountMenu.classList.remove('is-open', 'is-closing');
        elements.accountMenu.style.display = 'none';
    }

    function closeAccountMenu() {
        const menu = elements.accountMenu;
        if (!menu || menu.style.display !== 'flex' || menu.classList.contains('is-closing')) return;
        menu.classList.remove('is-open');
        menu.classList.add('is-closing');
        if (getComputedStyle(menu).animationName === 'none') finishAccountClose();
    }

    if (elements.accountMenu) {
        elements.accountMenu.addEventListener('animationend', (e) => {
            if (e.target === elements.accountMenu && e.animationName === 'account-menu-out') finishAccountClose();
        });
    }

    function hideAllMenus() {
        closeNotifTile();
        closeAccountMenu();
        if (elements.btnNotif) elements.btnNotif.classList.remove('is-open');
        if (elements.btnAccount) elements.btnAccount.classList.remove('is-open');
    }

    function toggleMenu(e, targetMenu) {
        e.preventDefault();
        e.stopPropagation();

        const isNotif = targetMenu === elements.notifTile;
        const isCurrentlyVisible = isNotif
            ? !targetMenu.classList.contains('is-hidden') && !targetMenu.classList.contains('is-closing')
            : targetMenu.style.display === 'flex' && !targetMenu.classList.contains('is-closing');

        hideAllMenus();

        if (!isCurrentlyVisible) {
            if (isNotif) {
                targetMenu.classList.remove('is-hidden', 'is-closing');
                elements.btnNotif.classList.add('is-open');
            } else {
                targetMenu.classList.remove('is-closing');
                targetMenu.classList.add('is-open');
                elements.btnAccount.classList.add('is-open');
                targetMenu.style.display = 'flex';
            }
        }
    }

    function playClickAnimation(btn) {
        btn.classList.remove('is-clicked');
        void btn.offsetWidth; // restart animation on rapid clicks
        btn.classList.add('is-clicked');
    }

    [elements.btnNotif, elements.btnAccount].forEach((btn) => {
        if (!btn) return;
        btn.addEventListener('animationend', (e) => {
            if (e.animationName === 'notif-btn-click') btn.classList.remove('is-clicked');
        });
    });

    if (elements.btnNotif && elements.notifTile) {
        elements.btnNotif.addEventListener('click', (e) => {
            playClickAnimation(elements.btnNotif);
            toggleMenu(e, elements.notifTile);
        });
    }
    if (elements.btnAccount && elements.accountMenu) {
        elements.btnAccount.addEventListener('click', (e) => {
            playClickAnimation(elements.btnAccount);
            toggleMenu(e, elements.accountMenu);
        });
    }

    document.addEventListener('click', (e) => {
        const isInsideNotif = elements.notifTile && elements.notifTile.contains(e.target);
        const isInsideAccount = elements.accountMenu && elements.accountMenu.contains(e.target);
        if (!isInsideNotif && !isInsideAccount) hideAllMenus();
    });

    // --- Fullscreen Logic ---
    // Browsers exit fullscreen on every page navigation and only allow re-entering
    // from a user gesture, so we remember the preference and restore it on the
    // first click/keypress of the next page.
    const FULLSCREEN_KEY = 'microspace.fullscreen';
    const getFullscreenEl = () => document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
    const storeFullscreenPref = (value) => {
        try { value ? localStorage.setItem(FULLSCREEN_KEY, '1') : localStorage.removeItem(FULLSCREEN_KEY); } catch (e) { /* storage unavailable */ }
    };
    const wantsFullscreen = () => {
        try { return localStorage.getItem(FULLSCREEN_KEY) === '1'; } catch (e) { return false; }
    };
    const enterFullscreen = () => {
        const doc = document.documentElement;
        return (doc.requestFullscreen || doc.mozRequestFullScreen || doc.webkitRequestFullscreen || doc.msRequestFullscreen)?.call(doc);
    };
    const exitFullscreen = () => (document.exitFullscreen || document.mozCancelFullScreen || document.webkitExitFullscreen || document.msExitFullscreen)?.call(document);

    function syncFullscreenIcon() {
        isFullscreen = !!getFullscreenEl();
        // A fullscreenchange only fires on the live page, so leaving here means the
        // user exited deliberately (Esc or the button), not via navigation.
        if (!isFullscreen) storeFullscreenPref(false);
        const icon = elements.fullscreenBtn?.querySelector('i');
        if (icon) {
            icon.classList.toggle('bi-fullscreen', !isFullscreen);
            icon.classList.toggle('bi-fullscreen-exit', isFullscreen);
        }
    }

    if (elements.fullscreenBtn) {
        elements.fullscreenBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // don't also trigger the restore-on-first-gesture handler
            if (!getFullscreenEl()) {
                storeFullscreenPref(true);
                enterFullscreen();
            } else {
                storeFullscreenPref(false);
                exitFullscreen();
            }
        });

        ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach((evt) => {
            document.addEventListener(evt, syncFullscreenIcon);
        });

        // Restore fullscreen after navigation on the first user gesture.
        // Capture phase so it runs even if other handlers stop propagation.
        if (wantsFullscreen() && !getFullscreenEl()) {
            const restore = () => {
                document.removeEventListener('click', restore, true);
                document.removeEventListener('keydown', restore, true);
                if (wantsFullscreen() && !getFullscreenEl()) enterFullscreen()?.catch?.(() => { });
            };
            document.addEventListener('click', restore, true);
            document.addEventListener('keydown', restore, true);
        }
    }

    // --- Alerts Logic ---
    function closeAlert() {
        if (elements.alertContainer && elements.alertContainer.length) {
            elements.alertContainer.fadeOut(400, function () {
                $(this).remove();
            });
        }
    }
    if (typeof $ !== 'undefined') {
        $(document).on('click', '.close-btn', closeAlert);
        if (elements.alertContainer.length) setTimeout(closeAlert, 5000);
    }

    function timeAgo(dateString) {
        if (!dateString) return '';
        const then = new Date(dateString);
        if (isNaN(then)) return '';

        const secs = Math.floor((Date.now() - then.getTime()) / 1000);
        if (secs < 0 || secs < 60) return 'Baru saja';

        const mins = Math.floor(secs / 60);
        if (mins < 60) return `${mins} menit lalu`;

        const hours = Math.floor(mins / 60);
        if (hours < 24) return `${hours} jam lalu`;

        const days = Math.floor(hours / 24);
        if (days < 7) return `${days} hari lalu`;
        if (days < 30) return `${Math.floor(days / 7)} minggu lalu`;
        if (days < 365) return `${Math.floor(days / 30)} bulan lalu`;

        return `${Math.floor(days / 365)} tahun lalu`;
    }

    // --- Real-time Notification Fetch ---
    function fetchNotifications() {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const token = csrfMeta ? csrfMeta.getAttribute('content') : '';
        const container = document.getElementById('notification-list');

        if (!container) return;

        fetch('/notifications')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(notifications => {
                const unreadCount = notifications.filter(
                    n => n.isread === 'no' || n.isread === 'unread'
                ).length;

                const bellBadge = document.getElementById('bell-notification-badge');
                if (bellBadge) {
                    if (unreadCount > 0) {
                        bellBadge.innerText = unreadCount;
                        bellBadge.style.setProperty('display', 'flex', 'important');
                    } else {
                        bellBadge.style.display = 'none';
                    }
                }

                if (notifications.length === 0) {
                    container.innerHTML = `
                    <div class="notif-empty-state text-center py-3">
                        <span class="material-symbols-outlined opacity-50">notification_audio_off</span>
                        <p class="text-muted small mb-0">No Notification yet</p>
                    </div>
                `;
                    const oldPop = document.querySelector('.notification-pop');
                    if (oldPop) oldPop.classList.add('hidden');
                    return;
                }

                let htmlContent = '';
                notifications.forEach((notification) => {
                    const systemLogo = '/images/logo-layout.png';
                    const isUnread = notification.isread === 'no' || notification.isread === 'unread';
                    const unreadClass = isUnread ? 'unread' : 'read';
                    const createdAt = timeAgo(notification.created_at);
                    const unreadDot = isUnread ? `
                    <div class="unread-lighter" style="width: 8px; height: 8px; background: #ff3e3e; border-radius: 50%; margin-left: 8px; flex-shrink: 0; margin-top: 5px; box-shadow: 0 0 8px #ff3e3e;"></div>
                ` : '';
                    const readStyle = !isUnread ? 'opacity: 0.6;' : '';

                    htmlContent += `
                    <form action="/notifications/${notification.id}/mark-as-read" method="POST" class="no-disable notif-action-form" style="width: 100%; margin: 0; ${readStyle}">
                        <input type="hidden" name="_token" value="${token}">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit" class="notification-content ${unreadClass}" style="background: none; border: none; padding: 12px; display: flex; text-align: left; width: 100%;">
                            <div class="sender-pp-container" style="margin-right: 12px; flex-shrink: 0;">
                                <div class="sender-pp" style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
                                    <img src="${systemLogo}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            </div>
                            <div class="notif-main-content" style="flex-grow: 1; display: flex; flex-direction: column; min-width: 0;">
                                <div class="notif-title" style="margin-bottom: 2px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                                    <p style="color: white; font-weight: 700; font-size: 0.9rem; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${notification.title}</p>
                                    ${createdAt ? `<p class="notif-meta" style="margin: 0; flex-shrink: 0;"><i class="bi bi-clock"></i>${createdAt}</p>` : ''}
                                </div>
                                <div class="notif-message">
                                    <p style="color: rgba(255,255,255,0.6); font-size: 0.8rem; margin: 0; line-height: 1.3;">${notification.message}</p>
                                </div>
                            </div>
                            ${unreadDot}
                        </button>
                    </form>
                `;
                });

                container.innerHTML = htmlContent;
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    fetchNotifications();
    setInterval(() => {
        fetchNotifications();
        console.log('Notifications safely refreshed');
    }, 30000);

    if (typeof window.initGlobalDropzones === 'function') {
        window.initGlobalDropzones();
    }
});

window.initGlobalDropzones = function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const zones = [];

    document.querySelectorAll('.custom-dropzone-box').forEach(el => {
        if (el.dropzone) return;

        const dz = new Dropzone(`#${el.id}`, {
            url: '#',
            autoProcessQueue: false,
            uploadMultiple: false,
            autoQuote: true,
            maxFilesize: 2,
            maxFiles: 1,
            acceptedFiles: el.dataset.accept || "image/*,application/pdf",
            addRemoveLinks: true,
            dictRemoveFile: "Hapus",
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                this.on("addedfile", async function (file) {
                    if (file.isCompressed) return;
                    try {
                        const compressed = await window.compressAndConvertImage(file);
                        if (compressed !== file) {
                            compressed.isCompressed = true;
                            this.removeFile(file);
                            this.addFile(compressed);
                        } else {
                            file.isCompressed = true;
                        }
                    } catch (e) {
                        console.error('Dropzone file compression error:', e);
                        file.isCompressed = true;
                    }
                });
            }
        });

        zones.push({
            dz,
            fieldName: el.dataset.name,
            form: el.closest('form')
        });
    });

    const forms = [...new Set(zones.map(z => z.form).filter(Boolean))];

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const fd = new FormData(form);

            zones.filter(z => z.form === form).forEach(z => {
                const files = z.dz.getAcceptedFiles();
                if (files.length) fd.set(z.fieldName, files[0]);
            });

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: fd
            })
                .then(async res => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error(data.message || 'Gagal menyimpan.');
                    window.location.href = data.redirect_url || '/users';
                })
                .catch(err => {
                    alert(err.message);
                    const btn = document.getElementById('houseSubmit');
                    if (btn) {
                        btn.disabled = false;
                        btn.querySelector('.btn-text').style.display = 'flex';
                        btn.querySelector('.spinner').style.display = 'none';
                    }
                });
        });
    });
};

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ajaxError(function (event, jqXHR, ajaxSettings, thrownError) {
        if (jqXHR.status === 419 || jqXHR.status === 401) {
            if (typeof window.handleSessionExpired === 'function') {
                window.handleSessionExpired();
            } else {
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                window.location.href = '/login';
            }
        }
    });

    const $inlineSearch = $('#inlineNavbarSearch');
    const $input = $('#routeSearchInput');
    const $resultsContainer = $('#routeSearchResults');

    let shiftPressTime = 0;
    let shiftUsedAsModifier = false;
    let lastShiftTapTime = 0;
    let searchDebounce = null;
    let selectedIndex = -1;

    let closeTimer = null;

    function isSearchOpen() {
        return !$inlineSearch.hasClass('d-none') && !$inlineSearch.hasClass('is-closing');
    }

    function openNavbarSearch() {
        clearTimeout(closeTimer);
        $inlineSearch.removeClass('is-closing d-none');
        $input.val('').focus();
        selectedIndex = -1;
        fetchRoutes('');
    }

    function closeNavbarSearch() {
        if (!isSearchOpen()) return;

        $inlineSearch.addClass('is-closing');
        // Timer doubles as fallback when animations are disabled (reduced motion)
        closeTimer = setTimeout(function () {
            $inlineSearch.removeClass('is-closing').addClass('d-none');
        }, 230);
    }

    // 1. Trigger via Search Icon Click
    $('#triggerGlobalSearch, #triggerGlobalSearchMobile, .triggerGlobalSearch').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openNavbarSearch();
    });

    // 2. Trigger via Close Icon Click
    $('#closeNavbarSearch').on('click', function () {
        closeNavbarSearch();
    });

    // 3. Shortcuts: Double Shift, Ctrl+K / Cmd+K, or ESC
    $(document).on('keydown', function (e) {
        if (e.key === 'Shift') {
            if (!shiftPressTime) {
                shiftPressTime = Date.now();
                shiftUsedAsModifier = false;
            }
        } else {
            shiftUsedAsModifier = true;
            lastShiftTapTime = 0;
        }

        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            if (!isSearchOpen()) {
                openNavbarSearch();
            } else {
                closeNavbarSearch();
            }
        }

        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'f') {
            const $pagesearchinput = $('input[name="search"], input[type="search"], input[name*="search" i], input[id*="search" i], input.search, input.search-input, .dataTables_filter input, input[placeholder*="cari" i], input[placeholder*="search" i]')
                .not('#inlineNavbarSearch input')
                .filter(':visible:not(:disabled):not([readonly])')
                .first();

            if ($pagesearchinput.length) {
                e.preventDefault();
                $pagesearchinput.focus().select();
            }
        }

        if (e.key === 'Escape' && isSearchOpen()) {
            closeNavbarSearch();
        }
    });

    $(document).on('keyup', function (e) {
        if (e.key === 'Shift') {
            const now = Date.now();
            const duration = now - shiftPressTime;
            shiftPressTime = 0;

            const isTyping = $(e.target).is('input, textarea, select, [contenteditable="true"]');
            if (isTyping && !$(e.target).is('#routeSearchInput')) {
                lastShiftTapTime = 0;
                return;
            }

            if (shiftUsedAsModifier || duration > 250) {
                lastShiftTapTime = 0;
                return;
            }

            if (now - lastShiftTapTime < 350) {
                e.preventDefault();
                if (!isSearchOpen()) {
                    openNavbarSearch();
                } else {
                    closeNavbarSearch();
                }
                lastShiftTapTime = 0;
            } else {
                lastShiftTapTime = now;
            }
        }
    });

    // Close when clicking outside inline search box
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#inlineNavbarSearch, #triggerGlobalSearch').length) {
            closeNavbarSearch();
        }
    });

    // 4. Input Listener with Debounce
    $input.on('keyup input', function (e) {
        if (['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) return;

        clearTimeout(searchDebounce);
        const query = $(this).val();

        searchDebounce = setTimeout(function () {
            fetchRoutes(query);
        }, 200);
    });

    // 5. Arrow Key & Enter Navigation
    $input.on('keydown', function (e) {
        const $items = $resultsContainer.find('.search-route-item');
        if ($items.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % $items.length;
            updateSelection($items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + $items.length) % $items.length;
            updateSelection($items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && $items.eq(selectedIndex).length) {
                window.location.href = $items.eq(selectedIndex).attr('href');
            } else if ($items.first().length) {
                window.location.href = $items.first().attr('href');
            }
        }
    });

    function updateSelection($items) {
        $items.removeClass('active text-white')
            .addClass('text-body');

        $items.find('.search-icon-box').removeClass('bg-white text-primary').addClass('bg-body-tertiary text-secondary');
        $items.find('.search-subtext').removeClass('text-white-50').addClass('text-secondary');

        if (selectedIndex >= 0 && $items.eq(selectedIndex).length) {
            const $active = $items.eq(selectedIndex);
            $active.addClass('active text-white')
                .removeClass('text-body');

            $active.find('.search-icon-box').removeClass('bg-body-tertiary text-secondary').addClass('bg-white text-primary');
            $active.find('.search-subtext').removeClass('text-secondary').addClass('text-white-50');

            $active[0].scrollIntoView({
                block: 'nearest'
            });
        }
    }

    function highlightText(query, text) {
        if (!query || !text) return text || '';
        const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return text.replace(new RegExp(`(${escaped})`, 'gi'), '<mark>$1</mark>');
    }

    // 6. Fetch Routes via AJAX
    function fetchRoutes(query) {
        const searchUrl = $('meta[name="menu-search-url"]').attr('content') || '/search-menu';
        $.ajax({
            url: searchUrl,
            type: "GET",
            data: {
                q: query
            },
            success: function (data) {
                $resultsContainer.empty();
                selectedIndex = -1;

                if (!data || data.length === 0) {
                    $resultsContainer.html(
                        '<div class="text-center py-4 text-secondary" style="font-size: 0.75rem;">No matching routes found.</div>'
                    );
                    return;
                }

                $.each(data, function (index, item) {
                    let iconHtml = '';

                    // Check if icon string belongs to Google Material Symbols
                    if (item.icon && item.icon.startsWith('material-symbols-outlined:')) {
                        const iconName = item.icon.replace('material-symbols-outlined:', '');
                        iconHtml = `<span class="material-symbols-outlined fs-5">${iconName}</span>`;
                    } else {
                        iconHtml = `<i class="${item.icon || 'fa-solid fa-link'} fs-6"></i>`;
                    }

                    const html = `
                        <a href="${item.url}" class="search-route-item d-flex align-items-center justify-content-between p-2 rounded text-decoration-none text-body transition mb-1">
                            <div class="d-flex align-items-center gap-3">
                                <div class="search-icon-box d-flex align-items-center justify-content-center rounded bg-body-tertiary text-secondary p-2" style="width: 34px; height: 34px;">
                                    ${iconHtml}
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold fs-6 mb-0">${highlightText(query, item.title)}</span>
                                    <small class="search-subtext text-secondary font-monospace" style="font-size: 0.7rem;">${highlightText(query, item.description || item.name)}</small>
                                </div>
                            </div>
                        <span class="badge bg-body-tertiary text-secondary font-monospace fw-normal">${highlightText(query, item.uri)}</span>
                        </a>
                    `;
                    $resultsContainer.append(html);
                });
            },
            error: function (xhr, status, error) {
                console.error("Menu Search AJAX Error:", xhr.status, xhr.responseText);
                $resultsContainer.html(
                    `<div class="text-center py-4 text-danger" style="font-size: 0.75rem;">
                        Error fetching routes (${xhr.status}). Check console for details.
                    </div>`
                );
            }
        });
    }
});

// Bottom bar click "pop" animation (mobile): let it play before navigating
document.addEventListener('click', function (e) {
    const item = e.target.closest('.bottom-bar-item');
    if (!item || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
    e.preventDefault();
    item.classList.remove('clicked');
    void item.offsetWidth;
    item.classList.add('clicked');
    const href = item.getAttribute('href');
    setTimeout(function () {
        if (href && href !== '#') window.location.href = href;
    }, 300);
});

// Mobile top bar back button: pop animation, then go back
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.mobile-back-btn');
    if (!btn) return;
    btn.classList.remove('clicked');
    void btn.offsetWidth;
    btn.classList.add('clicked');
    setTimeout(function () {
        window.history.back();
    }, 300);
});
