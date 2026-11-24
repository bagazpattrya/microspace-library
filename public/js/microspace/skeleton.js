/**
 * ====================================================================
 * Reusable Skeleton Loader JavaScript Helper
 * Global Object: window.Skeleton
 * Compatible with Vanilla JS, jQuery, Bootstrap, and Axios/Fetch
 * Cross-project reusable: Vehicle Management, Kane Backoffice, etc.
 * ====================================================================
 */

(function (window) {
    'use strict';

    const Skeleton = {
        /**
         * Resolves a target selector, jQuery object, or HTMLElement
         */
        _getElement(target) {
            if (!target) return null;
            if (typeof target === 'string') {
                return document.querySelector(target);
            }
            if (window.jQuery && target instanceof window.jQuery) {
                return target[0] || null;
            }
            if (target instanceof HTMLElement) {
                return target;
            }
            return null;
        },

        /**
         * Generate Table Skeleton HTML
         */
        table(options = {}) {
            const {
                rows = 5,
                cols = 4,
                hasHeader = true,
                hasAction = true,
                className = ''
            } = options;

            let colWidths = ['w-25', 'w-50', 'w-75', 'w-35', 'w-65'];

            let html = `<div class="skeleton-table-wrapper ${className}">
                <table class="skeleton-table table">`;

            if (hasHeader) {
                html += `<thead><tr>`;
                for (let c = 0; c < cols; c++) {
                    const widthClass = colWidths[c % colWidths.length];
                    html += `<th><span class="skeleton-line ${widthClass}"></span></th>`;
                }
                if (hasAction) {
                    html += `<th style="width: 100px; text-align: center;"><span class="skeleton-line w-50 mx-auto"></span></th>`;
                }
                html += `</tr></thead>`;
            }

            html += `<tbody>`;
            html += this.tableRows({ rows, cols, hasAction });
            html += `</tbody></table></div>`;

            return html;
        },

        /**
         * Generate only <tr><td>...</td></tr> for injecting into an existing <tbody>
         */
        tableRows(options = {}) {
            const { rows = 5, cols = 4, hasAction = true } = options;
            const widths = ['w-35', 'w-65', 'w-50', 'w-75', 'w-25'];
            let html = '';

            for (let r = 0; r < rows; r++) {
                html += `<tr>`;
                for (let c = 0; c < cols; c++) {
                    const widthClass = widths[(r + c) % widths.length];
                    if (c === 0) {
                        html += `<td>
                            <div class="skeleton-td-inner">
                                <span class="skeleton-avatar sm"></span>
                                <span class="skeleton-line ${widthClass}"></span>
                            </div>
                        </td>`;
                    } else {
                        html += `<td><span class="skeleton-line ${widthClass}"></span></td>`;
                    }
                }
                if (hasAction) {
                    html += `<td style="text-align: center;">
                        <span class="skeleton-btn sm" style="width: 65px;"></span>
                    </td>`;
                }
                html += `</tr>`;
            }
            return html;
        },

        /**
         * Generate Cards Grid Skeleton HTML
         */
        cards(options = {}) {
            const {
                count = 3,
                cols = 3,
                hasImage = false,
                lines = 3,
                hasFooter = true,
                className = ''
            } = options;

            const colClass = cols === 1 ? 'col-12' :
                             cols === 2 ? 'col-md-6 col-12' :
                             cols === 4 ? 'col-xl-3 col-md-6 col-12' :
                             'col-lg-4 col-md-6 col-12';

            let html = `<div class="row g-3 ${className}">`;

            for (let i = 0; i < count; i++) {
                html += `<div class="${colClass}">
                    <div class="skeleton-card h-100">`;

                if (hasImage) {
                    html += `<div class="skeleton-thumb mb-3"></div>`;
                }

                html += `<div class="skeleton-card-header">
                            <span class="skeleton-title mb-0" style="width: 60%;"></span>
                            <span class="skeleton-badge"></span>
                        </div>
                        <div class="skeleton-card-body">`;

                for (let l = 0; l < lines; l++) {
                    const width = l === lines - 1 ? 'w-50' : (l % 2 === 0 ? 'w-85' : 'w-100');
                    html += `<span class="skeleton-line ${width}"></span>`;
                }

                html += `</div>`;

                if (hasFooter) {
                    html += `<div class="skeleton-card-footer">
                                <span class="skeleton-line w-25 mb-0"></span>
                                <span class="skeleton-btn sm"></span>
                             </div>`;
                }

                html += `</div></div>`;
            }

            html += `</div>`;
            return html;
        },

        /**
         * Generate Stat / Metric Cards Skeleton HTML
         */
        stats(options = {}) {
            const { count = 4, cols = 4, className = '' } = options;

            const colClass = cols === 1 ? 'col-12' :
                             cols === 2 ? 'col-md-6 col-12' :
                             cols === 3 ? 'col-lg-4 col-md-6 col-12' :
                             'col-xl-3 col-md-6 col-12';

            let html = `<div class="row g-3 ${className}">`;

            for (let i = 0; i < count; i++) {
                html += `<div class="${colClass}">
                    <div class="skeleton-stat-card">
                        <div class="skeleton stat-icon"></div>
                        <div class="stat-info">
                            <span class="skeleton-line w-50 sm mb-1"></span>
                            <span class="skeleton-line w-75 lg mb-0"></span>
                        </div>
                    </div>
                </div>`;
            }

            html += `</div>`;
            return html;
        },

        /**
         * Generate List Skeleton HTML
         */
        list(options = {}) {
            const {
                count = 4,
                hasAvatar = true,
                lines = 2,
                hasAction = true,
                className = ''
            } = options;

            let html = `<div class="skeleton-list-container ${className}">`;

            for (let i = 0; i < count; i++) {
                html += `<div class="skeleton-list-item">
                    <div class="skeleton-list-left">`;

                if (hasAvatar) {
                    html += `<span class="skeleton-avatar"></span>`;
                }

                html += `<div class="skeleton-list-meta">
                            <span class="skeleton-line ${i % 2 === 0 ? 'w-65' : 'w-50'}"></span>`;

                for (let l = 1; l < lines; l++) {
                    html += `<span class="skeleton-line sm w-35"></span>`;
                }

                html += `</div></div>`;

                if (hasAction) {
                    html += `<div><span class="skeleton-badge"></span></div>`;
                }

                html += `</div>`;
            }

            html += `</div>`;
            return html;
        },

        /**
         * Generate Form Skeleton HTML
         */
        form(options = {}) {
            const { fields = 4, hasButtons = true, cols = 1, className = '' } = options;
            const colClass = cols === 2 ? 'col-md-6 col-12' : 'col-12';

            let html = `<div class="skeleton-form-container ${className}">
                <div class="row">`;

            for (let i = 0; i < fields; i++) {
                html += `<div class="${colClass}">
                    <div class="skeleton-form-group">
                        <div class="skeleton skeleton-form-label"></div>
                        <div class="skeleton skeleton-input"></div>
                    </div>
                </div>`;
            }

            html += `</div>`;

            if (hasButtons) {
                html += `<div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top" style="border-color: var(--skeleton-border)!important;">
                    <div class="skeleton skeleton-btn" style="width: 80px;"></div>
                    <div class="skeleton skeleton-btn" style="width: 120px;"></div>
                </div>`;
            }

            html += `</div>`;
            return html;
        },

        /**
         * Generate Detail / Profile Skeleton HTML
         */
        detail(options = {}) {
            const { className = '' } = options;

            return `<div class="skeleton-detail-card ${className}">
                <div class="skeleton-detail-header">
                    <div class="skeleton skeleton-avatar xl"></div>
                    <div class="d-flex flex-column gap-2 flex-grow-1">
                        <div class="skeleton skeleton-title mb-0" style="width: 40%;"></div>
                        <div class="skeleton skeleton-line w-25 sm mb-0"></div>
                        <div class="d-flex gap-2 mt-1">
                            <div class="skeleton skeleton-badge"></div>
                            <div class="skeleton skeleton-badge"></div>
                        </div>
                    </div>
                </div>
                <div class="skeleton-detail-grid">
                    ${Array.from({ length: 6 }).map(() => `
                        <div>
                            <div class="skeleton skeleton-line w-35 sm mb-1"></div>
                            <div class="skeleton skeleton-line w-75 mb-0"></div>
                        </div>
                    `).join('')}
                </div>
            </div>`;
        },

        /**
         * Generate Text Paragraph Skeleton HTML
         */
        text(options = {}) {
            const { lines = 3, hasTitle = true, className = '' } = options;
            let html = `<div class="${className}">`;

            if (hasTitle) {
                html += `<div class="skeleton skeleton-title"></div>`;
            }

            for (let l = 0; l < lines; l++) {
                const width = l === lines - 1 ? 'w-50' : (l % 2 === 0 ? 'w-85' : 'w-100');
                html += `<div class="skeleton skeleton-line ${width}"></div>`;
            }

            html += `</div>`;
            return html;
        },

        /**
         * Generate Custom Box Skeleton HTML
         */
        box(options = {}) {
            const {
                width = '100%',
                height = '120px',
                radius = '8px',
                className = ''
            } = options;

            return `<div class="skeleton skeleton-box ${className}" style="width: ${width}; height: ${height}; border-radius: ${radius};"></div>`;
        },

        /**
         * Generate Dashboard Skeleton HTML
         */
        dashboard(options = {}) {
            const { className = '' } = options;
            return `<div class="skeleton-dashboard-wrapper ${className}">
                <div class="big-screen">
                    <div class="skeleton mb-3" style="width: 280px; height: 32px; border-radius: 8px;"></div>
                    <div class="skeleton-card mb-4" style="padding: 1.25rem; border-radius: 14px;">
                        <div class="skeleton mb-4" style="width: 100%; height: 160px; border-radius: 12px;"></div>
                        <div class="skeleton mb-3" style="width: 70px; height: 18px; border-radius: 4px;"></div>
                        <div class="d-flex flex-wrap gap-4 align-items-center justify-content-between px-2 py-1">
                            ${Array.from({ length: 8 }).map(() => `
                                <div class="d-flex flex-column align-items-center gap-2" style="min-width: 60px;">
                                    <div class="skeleton" style="width: 52px; height: 52px; border-radius: 14px;"></div>
                                    <div class="skeleton-line" style="width: 55px; height: 11px; margin: 0; border-radius: 3px;"></div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-6 col-12">
                            <div class="skeleton-card h-100" style="padding: 1.25rem; border-radius: 14px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="skeleton" style="width: 140px; height: 20px; border-radius: 5px;"></div>
                                    <div class="skeleton-badge" style="width: 90px; height: 22px;"></div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="skeleton-stat-card" style="padding: 0.85rem; border-radius: 10px;">
                                            <div class="skeleton stat-icon" style="width: 42px; height: 42px; border-radius: 10px;"></div>
                                            <div class="stat-info">
                                                <span class="skeleton-line w-50 sm mb-1"></span>
                                                <span class="skeleton-line w-75 lg mb-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="skeleton-stat-card" style="padding: 0.85rem; border-radius: 10px;">
                                            <div class="skeleton stat-icon" style="width: 42px; height: 42px; border-radius: 10px;"></div>
                                            <div class="stat-info">
                                                <span class="skeleton-line w-50 sm mb-1"></span>
                                                <span class="skeleton-line w-75 lg mb-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    ${Array.from({ length: 3 }).map(() => `
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: var(--skeleton-base); border-radius: 8px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="skeleton" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                                <div>
                                                    <div class="skeleton-line mb-1" style="width: 120px; height: 12px;"></div>
                                                    <div class="skeleton-line sm mb-0" style="width: 80px; height: 10px;"></div>
                                                </div>
                                            </div>
                                            <div class="skeleton-badge" style="width: 70px; height: 20px;"></div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="skeleton-card h-100" style="padding: 1.25rem; border-radius: 14px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="skeleton" style="width: 160px; height: 20px; border-radius: 5px;"></div>
                                    <div class="skeleton-badge" style="width: 80px; height: 22px;"></div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="skeleton-stat-card" style="padding: 0.85rem; border-radius: 10px;">
                                            <div class="skeleton stat-icon" style="width: 42px; height: 42px; border-radius: 10px;"></div>
                                            <div class="stat-info">
                                                <span class="skeleton-line w-50 sm mb-1"></span>
                                                <span class="skeleton-line w-75 lg mb-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="skeleton-stat-card" style="padding: 0.85rem; border-radius: 10px;">
                                            <div class="skeleton stat-icon" style="width: 42px; height: 42px; border-radius: 10px;"></div>
                                            <div class="stat-info">
                                                <span class="skeleton-line w-50 sm mb-1"></span>
                                                <span class="skeleton-line w-75 lg mb-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    ${Array.from({ length: 3 }).map(() => `
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background: var(--skeleton-base); border-radius: 8px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="skeleton" style="width: 36px; height: 36px; border-radius: 8px;"></div>
                                                <div>
                                                    <div class="skeleton-line mb-1" style="width: 140px; height: 12px;"></div>
                                                    <div class="skeleton-line sm mb-0" style="width: 90px; height: 10px;"></div>
                                                </div>
                                            </div>
                                            <div class="skeleton-badge" style="width: 75px; height: 20px;"></div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mobile-screen flex-column w-100" style="display: flex; flex-direction: column; width: 100%;">
                    <div class="skeleton mb-3" style="width: 100%; height: 170px; border-radius: 0 0 16px 16px;"></div>
                    <div class="skeleton-card mb-3 p-3 mx-auto" style="width: 95%; border-radius: 14px;">
                        <div class="row g-3 text-center">
                            ${Array.from({ length: 8 }).map(() => `
                                <div class="col-3 d-flex flex-column align-items-center gap-1">
                                    <div class="skeleton" style="width: 46px; height: 46px; border-radius: 12px;"></div>
                                    <div class="skeleton-line" style="width: 48px; height: 10px; margin: 0;"></div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="skeleton-card p-3 mx-auto mb-3" style="width: 95%; border-radius: 14px;">
                        <div class="skeleton mb-3" style="width: 130px; height: 18px; border-radius: 4px;"></div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><div class="skeleton" style="width: 100%; height: 50px; border-radius: 8px;"></div></div>
                            <div class="col-6"><div class="skeleton" style="width: 100%; height: 50px; border-radius: 8px;"></div></div>
                        </div>
                        <div class="skeleton" style="width: 100%; height: 42px; border-radius: 8px; margin-bottom: 8px;"></div>
                        <div class="skeleton" style="width: 100%; height: 42px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>`;
        },

        /**
         * Master Render Dispatcher
         */
        render(type = 'table', options = {}) {
            switch (type) {
                case 'table':
                    return this.table(options);
                case 'table-rows':
                case 'rows':
                    return this.tableRows(options);
                case 'card':
                case 'cards':
                    return this.cards(options);
                case 'stat':
                case 'stats':
                case 'metric':
                case 'metrics':
                    return this.stats(options);
                case 'list':
                    return this.list(options);
                case 'form':
                    return this.form(options);
                case 'detail':
                case 'profile':
                    return this.detail(options);
                case 'dashboard':
                    return this.dashboard(options);
                case 'text':
                    return this.text(options);
                case 'box':
                case 'custom':
                default:
                    return this.box(options);
            }
        },

        /**
         * Injects Skeleton loader into a target DOM element.
         * Saves previous HTML so it can be restored on hide().
         *
         * @param {string|HTMLElement|jQuery} target
         * @param {string} type ('table', 'table-rows', 'cards', 'stats', 'list', 'form', 'detail', 'text', 'box')
         * @param {Object} options
         */
        show(target, type = 'table', options = {}) {
            const el = this._getElement(target);
            if (!el) {
                console.warn('[Skeleton] Element not found for target:', target);
                return;
            }

            // Save original HTML if not already stored
            if (!el._skeleton_original_content) {
                el._skeleton_original_content = el.innerHTML;
            }

            el.innerHTML = this.render(type, options);
            el.setAttribute('data-skeleton-active', 'true');
        },

        /**
         * Hides or clears the skeleton loader from target.
         *
         * @param {string|HTMLElement|jQuery} target
         * @param {boolean} restoreOriginal - If true, restores HTML content prior to show()
         */
        hide(target, restoreOriginal = false) {
            const el = this._getElement(target);
            if (!el) return;

            if (restoreOriginal && el._skeleton_original_content !== undefined) {
                el.innerHTML = el._skeleton_original_content;
                delete el._skeleton_original_content;
            } else {
                el.innerHTML = '';
            }

            el.removeAttribute('data-skeleton-active');
        },

        /**
         * Automatically synchronizes the skeleton structure with the real page elements
         * (e.g. adjusts table columns, headers, badges, action buttons to match real table)
         */
        syncWithRealPage(skeletonEl) {
            if (!skeletonEl) return;

            // 1. Detect if the page has a real table
            const realTables = Array.from(document.querySelectorAll('.main-content-container table:not(.skeleton-table)'));
            const skeletonTableWrapper = skeletonEl.querySelector('.skeleton-table-wrapper');

            if (realTables.length > 0 && skeletonTableWrapper) {
                // Pick visible table for current responsive viewport
                let targetTable = realTables[0];
                for (const t of realTables) {
                    const parent = t.closest('.d-none, .d-lg-none, .d-md-none');
                    if (!parent || window.getComputedStyle(parent).display !== 'none') {
                        targetTable = t;
                        break;
                    }
                }

                const realThs = targetTable.querySelectorAll('thead tr th');
                if (realThs.length > 0) {
                    const skeletonTable = skeletonTableWrapper.querySelector('table');
                    if (skeletonTable) {
                        let newThead = '<thead><tr>';
                        realThs.forEach((th, idx) => {
                            const text = th.textContent.trim();
                            const isAction = idx === realThs.length - 1 || /aksi|action/i.test(text);
                            const alignCenter = th.classList.contains('text-center') || isAction;
                            const widthStyle = th.style.width ? `width: ${th.style.width};` : (isAction ? 'width: 80px;' : '');

                            newThead += `<th class="${alignCenter ? 'text-center' : ''}" style="${widthStyle}">
                                <span class="skeleton-line ${isAction ? 'w-50 mx-auto' : 'w-65'}"></span>
                            </th>`;
                        });
                        newThead += '</tr></thead>';

                        let newTbody = '<tbody>';
                        for (let r = 0; r < 8; r++) {
                            newTbody += '<tr>';
                            realThs.forEach((th, c) => {
                                const text = th.textContent.trim();
                                const isAction = c === realThs.length - 1 || /aksi|action/i.test(text);
                                const isBadge = /status|booking|tipe|kategori|role|badge/i.test(text);

                                if (isAction) {
                                    newTbody += '<td class="text-center"><span class="skeleton-btn sm" style="width: 32px; height: 30px; border-radius: 6px;"></span></td>';
                                } else if (isBadge) {
                                    newTbody += '<td><span class="skeleton-badge" style="width: 80px; height: 22px;"></span></td>';
                                } else {
                                    const widths = ['w-75', 'w-65', 'w-50', 'w-85'];
                                    newTbody += `<td><span class="skeleton-line ${widths[(r + c) % widths.length]}"></span></td>`;
                                }
                            });
                            newTbody += '</tr>';
                        }
                        newTbody += '</tbody>';

                        skeletonTable.innerHTML = newThead + newTbody;
                    }
                }
            }
        },

        /**
         * Initialize Global Page Load Transition for 100% accurate skeletons across ALL menus
         */
        initPageTransition() {
            const ghostBody = document.getElementById('main-content-body');
            const overlaySkeleton = document.getElementById('global-page-skeleton');

            if (!ghostBody && !overlaySkeleton) return;

            // If an overlay skeleton is present, adapt it if needed
            if (overlaySkeleton) {
                this.syncWithRealPage(overlaySkeleton);
            }

            let isDismissed = false;

            function dismiss() {
                if (isDismissed) return;
                isDismissed = true;

                // 1. Dismiss overlay skeleton
                if (overlaySkeleton) {
                    overlaySkeleton.classList.add('is-hidden');
                    setTimeout(() => {
                        overlaySkeleton.style.display = 'none';
                    }, 350);
                }

                // 2. Dismiss ghost shimmer on main-content-body
                if (ghostBody) {
                    ghostBody.classList.remove('page-skeleton-loading');
                    ghostBody.classList.add('page-skeleton-fade');
                    setTimeout(() => {
                        ghostBody.classList.remove('page-skeleton-fade');
                    }, 350);
                }
            }

            // Snappy display duration (300ms) so user perceives the elegant shimmer wave
            const minDelay = 300;
            const startTime = performance.now();

            function scheduleDismiss() {
                const elapsed = performance.now() - startTime;
                const remaining = Math.max(0, minDelay - elapsed);
                setTimeout(dismiss, remaining);
            }

            function onReady() {
                if (document.fonts && document.fonts.ready) {
                    document.fonts.ready.then(scheduleDismiss).catch(scheduleDismiss);
                } else {
                    scheduleDismiss();
                }
            }

            if (document.readyState === 'complete') {
                onReady();
            } else {
                window.addEventListener('load', onReady);
            }

            // Generous safety timeout: only force dismiss after 8s if an external CDN hangs
            setTimeout(scheduleDismiss, 8000);

            // Handle bfcache (browser back/forward navigation)
            window.addEventListener('pageshow', function (event) {
                if (event.persisted) {
                    dismiss();
                }
            });
        }
    };

    // Attach to global window
    window.Skeleton = Skeleton;

    // Auto-init global page transition if present
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Skeleton.initPageTransition());
    } else {
        Skeleton.initPageTransition();
    }

})(window);

