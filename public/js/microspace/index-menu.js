/* Index Menu Standard (index-menu.php section 4). Shared factory: IdxMenu.init({...}) per page.
 *
 * cfg:
 *   dataUrl         required  server-side data endpoint
 *   defaultSort     [serverColIndex, 'asc'|'desc']
 *   defaultStatus   string|null  (null = page has no status filter)
 *   statusParam     query name the controller reads for status (default 'status')
 *   params          object|function  extra page params sent on every request
 *   desktopColumns  DataTables columns for #idxTable
 *   mobileColumns   DataTables columns for #idxTableMobile
 *   mobileOrder     initial order of the mobile table (MOBILE column index)
 *   mobileToServer  { mobileColIndex: serverColIndex }
 *   onRowClick      function(rowData, event); default = go to rowData.show_url
 */
window.IdxMenu = (function () {
    const esc = v => String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const notify = (msg, type) => (typeof showAlertx === 'function' ? showAlertx(msg, type) : console.error(msg));

    function popApplied(btn) {
        if (!btn) return;
        btn.classList.remove('idx-applied'); void btn.offsetWidth; btn.classList.add('idx-applied');
        btn.addEventListener('animationend', e => { if (e.target === btn) btn.classList.remove('idx-applied'); }, { once: true });
    }

    function init(cfg) {
        const $ = window.jQuery;
        const o = Object.assign({
            statusParam: 'status', defaultStatus: null, defaultSort: [0, 'asc'], params: {},
            mobileOrder: [[0, 'asc']], mobileToServer: {}, onRowClick: null,
        }, cfg);
        const state = { status: o.defaultStatus, override: null, filters: {} };
        const $id = id => document.getElementById(id);

        // Never show the raw DataTables alert(); show the real cause instead.
        $.fn.dataTable.ext.errMode = 'none';
        $(document).on('error.dt', (e, settings, techNote, message) => {
            const x = settings.jqXHR;
            let detail = '';
            try { detail = x?.responseJSON?.message || (x?.responseText || '').replace(/<[^>]+>/g, ' ').trim().slice(0, 300); } catch (_) {}
            console.error('[index-menu] DataTables error:', message, x ? `HTTP ${x.status}` : '', x?.responseText?.slice(0, 1000));
            notify(`Gagal memuat data${x ? ` (HTTP ${x.status})` : ''}. ${detail}`, 'error');
        });

        const tableDefaults = {
            serverSide: true, processing: true, deferRender: true, searchDelay: 350, pageLength: 25,
            search: { search: $id('idxSearch').value },
            dom: "rt<'row mt-3 align-items-center'<'col-sm-12 col-md-5 text-secondary small'i><'col-sm-12 col-md-7 d-flex justify-content-md-end'p>>",
            language: {
                search: '', processing: '',
                zeroRecords: 'Data tidak ditemukan.', info: 'Menampilkan _START_ s/d _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data', infoFiltered: '(difilter dari _MAX_ data)', paginate: { previous: '&lsaquo;', next: '&rsaquo;' },
            },
            drawCallback: function () {
                this.api().rows({ page: 'current' }).nodes().each((tr, i) => { tr.classList.add('idx-row-in'); tr.style.animationDelay = `${Math.min(i, 15) * 25}ms`; });
            },
            createdRow: (tr, d) => { tr.dataset.id = d.id; },
        };

        const ajaxFor = isMobile => ({
            url: o.dataUrl, type: 'GET',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            dataSrc: json => {
                (json.summary || []).forEach((v, i) => { const el = document.querySelector(`[data-idx-summary="${i}"]`); if (el) el.textContent = v; });
                return json.data;
            },
            data: d => {
                Object.assign(d, typeof o.params === 'function' ? o.params() : o.params, state.filters);
                if (state.status !== null) d[o.statusParam] = state.status;
                if (isMobile) {
                    if (state.override) d.order = [state.override];
                    else d.order.forEach(c => { c.column = o.mobileToServer[c.column] ?? o.defaultSort[0]; });
                }
            },
        });

        let currentSort = o.defaultSort;
        function syncSortUi(col, dir) {
            currentSort = [col, dir];
            document.querySelectorAll('[data-idx-sort]').forEach(a => {
                const on = +a.dataset.idxSort === col;
                a.classList.toggle('active', on);
                if (on) {
                    $id('idxSortLabel').textContent = a.dataset.label;
                    $id('idxSortIcon').className = `bi bi-sort-${dir === 'desc' ? 'down' : 'up'}`;
                }
            });
        }

        // Skeleton placeholder rows for the initial load; DataTables empties tbody on first draw.
        function showSkeleton(sel, cols) {
            const body = document.querySelector(`${sel} tbody`);
            if (!body) return;
            const widths = [70, 45, 60, 80, 55, 65];
            body.innerHTML = Array.from({ length: 8 }, () => '<tr class="idx-skeleton-row">' +
                Array.from({ length: cols }, (_, c) => `<td><span class="idx-skeleton" style="width:${widths[c % widths.length]}%"></span></td>`).join('') + '</tr>').join('');
        }

        function initDesktopTable() {
            showSkeleton('#idxTable', o.desktopColumns.length);
            const t = $('#idxTable').DataTable({ ...tableDefaults, order: [o.defaultSort], ajax: ajaxFor(false), columns: o.desktopColumns });
            t.on('order.dt', () => { const ord = t.order()[0]; if (ord) syncSortUi(ord[0], ord[1]); });
            return t;
        }
        function initMobileTable() {
            showSkeleton('#idxTableMobile', o.mobileColumns.length);
            const t = $('#idxTableMobile').DataTable({ ...tableDefaults, order: o.mobileOrder, ajax: ajaxFor(true), columns: o.mobileColumns });
            t.on('order.dt', () => { state.override = null; });
            return t;
        }

        const desktopQuery = window.matchMedia('(min-width: 768px)');
        let desktopTable = null, mobileTable = null;
        const activeTable = () => (desktopQuery.matches ? desktopTable : mobileTable);
        function initVisibleTable() {
            if (desktopQuery.matches && !desktopTable) desktopTable = initDesktopTable();
            if (!desktopQuery.matches && !mobileTable) mobileTable = initMobileTable();
        }

        function applySort(col, dir) { // never call order() directly on the mobile table (its columns differ)
            syncSortUi(col, dir);
            popApplied($id('idxSortLabel').closest('.btn'));
            if (desktopQuery.matches) desktopTable.order([col, dir]).draw();
            else { state.override = { column: col, dir }; mobileTable.ajax.reload(); }
        }

        // Loading indicator: animate the search icon on every ajax; keep it visible at least 800 ms
        let searchingSince = 0, searchingEnd;
        $(document).on('preXhr.dt', function () {
            clearTimeout(searchingEnd);
            searchingSince = Date.now();
            document.querySelector('.idx-toolbar .input-group')?.classList.add('idx-searching');
        }).on('xhr.dt error.dt', function () {
            clearTimeout(searchingEnd);
            searchingEnd = setTimeout(() => document.querySelector('.idx-toolbar .input-group')?.classList.remove('idx-searching'), Math.max(0, 800 - (Date.now() - searchingSince)));
        });

        $(function () {
            initVisibleTable();
            desktopQuery.addEventListener('change', initVisibleTable);

            let timer;
            $('#idxSearch').on('input', function () { const q = this.value; clearTimeout(timer); timer = setTimeout(() => activeTable()?.search(q).draw(), 350); });

            // search icon = magnifier when empty, reload arrow when there is text
            const syncSearchIcon = () => {
                const has = $('#idxSearch').val().trim() !== '';
                const ico = $id('idxSearchIcon');
                if (ico.classList.contains('bi-arrow-clockwise') === has) return;
                ico.className = `bi ${has ? 'bi-arrow-clockwise' : 'bi-search'}`;
                $('#idxSearchBtn').attr({ 'aria-label': has ? 'Muat ulang' : 'Cari', title: has ? 'Muat ulang' : '' });
                ico.style.animation = 'idx-label-in .3s cubic-bezier(.34,1.56,.64,1)';
                ico.addEventListener('animationend', () => { ico.style.animation = ''; }, { once: true });
            };
            $('#idxSearch').on('input search', syncSearchIcon);
            syncSearchIcon();
            $('#idxSearchBtn').on('click', () => {
                if ($('#idxSearch').val().trim() === '') $('#idxSearch').trigger('focus');
                else activeTable()?.ajax.reload(null, false);
            });

            $(document).on('click', '[data-idx-status]', function (e) {
                e.preventDefault();
                state.status = this.dataset.idxStatus;
                $(this).closest('.dropdown-menu').find('.dropdown-item').removeClass('active'); this.classList.add('active');
                $id('idxStatusLabel').textContent = this.dataset.label;
                popApplied($id('idxStatusLabel').closest('.btn'));
                activeTable()?.ajax.reload();
            });

            // Single-select filter dropdown (rule 13)
            $(document).on('click', '[data-idx-filter-opt]', function (e) {
                e.preventDefault();
                const name = this.dataset.idxFilterOpt;
                if (this.dataset.value) state.filters[name] = this.dataset.value; else delete state.filters[name];
                $(this).closest('.dropdown-menu').find('.dropdown-item').removeClass('active'); this.classList.add('active');
                $id('idxFilterLabel').textContent = this.dataset.label;
                activeTable()?.ajax.reload();
            });

            // Filter drawer (rule 13): only for 2+ filter inputs
            const filterDrawer = $id('idxFilterDrawer');
            if (filterDrawer) {
                const fields = () => filterDrawer.querySelectorAll('[data-idx-filter]');
                $('#idxFilterApply').on('click', function () {
                    state.filters = {};
                    fields().forEach(f => { if (f.value) state.filters[f.dataset.idxFilter] = f.value; });
                    $id('idxFilterDot').classList.toggle('d-none', !Object.keys(state.filters).length);
                    activeTable()?.ajax.reload();
                    bootstrap.Offcanvas.getInstance(filterDrawer)?.hide();
                });
                $('#idxFilterReset').on('click', function () {
                    fields().forEach(f => { f.value = ''; });
                    state.filters = {};
                    $id('idxFilterDot').classList.add('d-none');
                    activeTable()?.ajax.reload();
                });
            }

            $(document).on('click', '[data-idx-sort]', function (e) {
                e.preventDefault();
                const col = +this.dataset.idxSort;
                applySort(col, currentSort[0] === col ? (currentSort[1] === 'desc' ? 'asc' : 'desc') : this.dataset.firstDir);
            });

            // row click -> detail (ignore links/buttons/forms)
            $('#idxTable tbody, #idxTableMobile tbody').on('click', 'tr', function (e) {
                if ($(e.target).closest('a, button, input, select, form').length) return;
                const d = $(this).closest('table').DataTable().row(this).data();
                if (!d) return;
                if (o.onRowClick) o.onRowClick(d, e);
                else if (d.show_url) window.location.href = d.show_url;
            });
        });

        return { activeTable, reload: () => activeTable()?.ajax.reload(null, false) };
    }

    return { init, esc, notify };
})();
