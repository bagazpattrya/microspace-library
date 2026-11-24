/**
 * Toolbox Adaptive Menu Layout
 * Menyesuaikan jumlah menu toolbox yang tampil (1 - 10) secara dinamis saat layar membesar atau mengecil
 * agar susunan baris tetap seimbang dan layout tidak rusak.
 */
(function () {
    'use strict';

    function adjustToolbox(container) {
        if (!container) return;

        // Jangan proses jika kontainer sedang tersembunyi (misal desktop vs mobile)
        const width = container.offsetWidth;
        if (width <= 0) return;

        const items = Array.from(container.querySelectorAll('.toolbox-adaptive-item'));
        const moreBtn = container.querySelector('.tool-item-more');
        const totalItems = items.length;

        if (totalItems === 0) return;

        let targetCols = 4;
        let targetVisibleItems = 7;

        // Cek apakah kontainer berada di tampilan mobile (< 650px atau di dalam .mobile-screen)
        const isMobileScreen = Boolean(container.closest('.mobile-screen')) || width < 650;

        // Penyesuaian berdasarkan lebar kontainer aktual:
        if (isMobileScreen) {
            // Evaluasi lebar kontainer mobile secara dinamis:
            if (width < 230) {
                // Layar HP ultra-kecil / split screen sangat sempit (< 230px kontainer):
                // 2 baris x 2 kolom = 4 slot (3 menu + 1 tombol Lainnya)
                targetCols = 2;
                targetVisibleItems = 3;
            } else if (width < 320) {
                // Layar HP compact / Fold cover screen / split window (230px - 319px kontainer):
                // 2 baris x 3 kolom = 6 slot (5 menu + 1 tombol Lainnya)
                targetCols = 3;
                targetVisibleItems = 5;
            } else if (width < 460) {
                // Layar HP portrait standar (320px - 459px kontainer):
                // 2 baris x 4 kolom = 8 slot (7 menu + 1 tombol Lainnya)
                targetCols = 4;
                targetVisibleItems = 7;
            } else {
                // Layar HP lebar / Phablet / Tablet / Split Window (>= 460px kontainer):
                // 2 baris x 5 kolom = 10 slot (9 menu + 1 tombol Lainnya)
                targetCols = 5;
                targetVisibleItems = 9;
            }
        } else if (width < 960) {
            // Layar tablet / desktop split (~650px - 959px):
            // Tampilkan 7 menu + tombol Lainnya = 1 baris pas (8 kolom)
            targetCols = 8;
            targetVisibleItems = 7;
        } else if (width < 1140) {
            // Layar desktop normal (~960px - 1139px):
            // Tampilkan 8 menu + tombol Lainnya = 1 baris pas (9 kolom)
            targetCols = 9;
            targetVisibleItems = 8;
        } else {
            // Layar desktop lebar (>= 1140px):
            // Tampilkan hingga 10 menu + tombol Lainnya dalam 1 baris pas
            targetCols = Math.min(11, totalItems + (moreBtn ? 1 : 0));
            targetVisibleItems = Math.min(10, totalItems);
        }

        // Jika jumlah total menu yang tersedia untuk role ini lebih sedikit:
        const countToShow = Math.min(targetVisibleItems, totalItems);

        // Jika jumlah item lebih sedikit dari target kolom, sesuaikan kolomnya agar tidak renggang berlebihan
        let actualCols = targetCols;
        if (countToShow + (moreBtn ? 1 : 0) < targetCols) {
            actualCols = countToShow + (moreBtn ? 1 : 0);
        }

        // Terapkan kolom grid ke kontainer
        container.style.setProperty('grid-template-columns', 'repeat(' + actualCols + ', 1fr)', 'important');

        // Tampilkan item urutan 1 s/d countToShow, sembunyikan sisanya
        items.forEach((item, idx) => {
            const order = idx + 1;
            if (order <= countToShow) {
                item.style.setProperty('display', 'flex', 'important');
                item.classList.remove('d-none');
            } else {
                item.style.setProperty('display', 'none', 'important');
                item.classList.add('d-none');
            }
        });

        // Tombol "Lainnya" selalu tampil di akhir
        if (moreBtn) {
            moreBtn.style.setProperty('display', 'flex', 'important');
            moreBtn.classList.remove('d-none');
        }
    }

    function initToolboxAdapters() {
        const containers = document.querySelectorAll('[data-role="toolbox-container"], .toolbox-container');
        if (!containers.length) return;

        containers.forEach(container => {
            adjustToolbox(container);

            if (window.ResizeObserver) {
                const observer = new ResizeObserver(entries => {
                    for (const entry of entries) {
                        adjustToolbox(entry.target);
                    }
                });
                observer.observe(container);
            }
        });

        window.addEventListener('resize', () => {
            containers.forEach(adjustToolbox);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToolboxAdapters);
    } else {
        initToolboxAdapters();
    }
})();
