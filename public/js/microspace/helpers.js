// microspace-v1.0.9a

/**
 * ----------------------------------------------------------------------
 * CORE UTILITIES (Pure JavaScript)
 * ----------------------------------------------------------------------
 */

/**
 * Format number into Indonesian Rupiah currency format.
 * @param {number|string} angka - The number value to format
 * @param {string} prefix - Optional currency prefix (e.g., 'Rp ')
 */
function formatRupiah(angka, prefix = '') {
    if (angka === undefined || angka === null || angka === '') return prefix + '0';

    let cleanNumber = parseFloat(angka.toString().replace(/[^0-9.-]/g, ''));
    if (isNaN(cleanNumber)) return prefix + '0';

    let formatted = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(cleanNumber);

    return prefix + formatted;
}

/**
 * Format a date string into 'dd-mm-yyyy' format.
 * @param {string} dateString - Valid date string format
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}-${month}-${year}`;
}

/**
 * Convert numbers into Indonesian word text representation.
 * @param {number|string} angka - The number to convert
 */
function terbilang(angka) {
    let bilangan = Math.abs(Math.floor(parseFloat(angka)));
    if (isNaN(bilangan)) return "";

    const kalimat = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

    if (bilangan < 12) {
        return " " + kalimat[bilangan];
    } else if (bilangan < 20) {
        return terbilang(bilangan - 10) + " Belas";
    } else if (bilangan < 100) {
        return terbilang(Math.floor(bilangan / 10)) + " Puluh" + terbilang(bilangan % 10);
    } else if (bilangan < 200) {
        return " Seratus" + terbilang(bilangan - 100);
    } else if (bilangan < 1000) {
        return terbilang(Math.floor(bilangan / 100)) + " Ratus" + terbilang(bilangan % 100);
    } else if (bilangan < 2000) {
        return " Seribu" + terbilang(bilangan - 1000);
    } else if (bilangan < 1000000) {
        return terbilang(Math.floor(bilangan / 1000)) + " Ribu" + terbilang(bilangan % 1000);
    } else if (bilangan < 1000000000) {
        return terbilang(Math.floor(bilangan / 1000000)) + " Juta" + terbilang(bilangan % 1000000);
    } else if (bilangan < 1000000000000) {
        return terbilang(Math.floor(bilangan / 1000000000)) + " Milyar" + terbilang(bilangan % 1000000000);
    } else if (bilangan < 1000000000000000) {
        return terbilang(Math.floor(bilangan / 1000000000000)) + " Triliun" + terbilang(bilangan % 1000000000000);
    }
    return "";
}

/**
 * ----------------------------------------------------------------------
 * JQUERY EXTENSIONS / PLUGINS
 * ----------------------------------------------------------------------
 */
if (typeof jQuery !== 'undefined') {
    (function ($) {
        $.fn.inputRupiah = function (prefix = '') {
            return this.each(function () {
                const $input = $(this);

                if ($input.val()) {
                    let clean = $input.val().replace(/[^0-9.-]/g, '');
                    $input.val(formatRupiah(clean, prefix));
                }

                // Tambahkan keydown listener untuk mendeteksi shortcut
                let isShortcutPressed = false;
                $input.on('keydown.helperRupiah', function (e) {
                    // Deteksi Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X atau Cmd (Mac) dan tombol navigasi Shift
                    if (e.ctrlKey || e.metaKey || e.shiftKey) {
                        isShortcutPressed = true;
                    } else {
                        isShortcutPressed = false;
                    }
                });

                $input.on('input.helperRupiah keyup.helperRupiah', function (e) {
                    // JIKA USER SEDANG PAI SHORTCUT (Ctrl+A dll), JANGAN FORMAT ATAU KUNCI KURSOR!
                    if (isShortcutPressed) {
                        return;
                    }

                    let cursorPosition = this.selectionStart;
                    let originalLength = $input.val().length;

                    let nilaiMentah = $input.val().replace(/[^0-9]/g, '');

                    if (nilaiMentah === '') {
                        $input.val('');
                        return;
                    }

                    let nilaiFormat = formatRupiah(nilaiMentah, prefix);
                    $input.val(nilaiFormat);

                    let newLength = nilaiFormat.length;
                    cursorPosition = cursorPosition + (newLength - originalLength);

                    // Kunci posisi kursor hanya jika user mengetik karakter biasa
                    this.setSelectionRange(cursorPosition, cursorPosition);
                });
            });
        };

        $.fn.textRupiah = function (prefix = '') {
            return this.each(function () {
                const $el = $(this);
                let nilaiMentah = $el.text().replace(/[^0-9.-]/g, '');
                if (nilaiMentah) {
                    $el.text(formatRupiah(nilaiMentah, prefix));
                }
            });
        };
    })(jQuery);
}

/**
 * ----------------------------------------------------------------------
 * IMAGE COMPRESSION 
 * ----------------------------------------------------------------------
 */

window.compressAndConvertImage = async function (file) {
    if (!file) return file;

    const MAX_EDGE = 1920;
    const JPEG_QUALITY = 0.82;
    const SKIP_UNDER_BYTES = 300 * 1024; // skip compression if file size < 300KB

    function loadImage(file) {
        return new Promise((resolve, reject) => {
            const url = URL.createObjectURL(file);
            const img = new Image();
            img.onload = () => {
                URL.revokeObjectURL(url);
                resolve(img);
            };
            img.onerror = () => {
                URL.revokeObjectURL(url);
                reject(new Error('Gagal membaca gambar'));
            };
            img.src = url;
        });
    }

    // 2. Compress image if it is of type image/*, not a gif, and exceeds SKIP_UNDER_BYTES
    if (!file.type.startsWith('image/') || file.type === 'image/gif' || file.size <= SKIP_UNDER_BYTES) {
        return file;
    }

    try {
        const img = await loadImage(file);
        let { width, height } = img;
        const longest = Math.max(width, height);

        if (longest > MAX_EDGE) {
            const scale = MAX_EDGE / longest;
            width = Math.round(width * scale);
            height = Math.round(height * scale);
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(img, 0, 0, width, height);

        const blob = await new Promise(resolve =>
            canvas.toBlob(resolve, 'image/jpeg', JPEG_QUALITY)
        );

        if (!blob || blob.size >= file.size) {
            return file;
        }

        const newName = file.name.replace(/\.[^.]+$/, '') + '.jpg';
        const finalFile = new File([blob], newName, {
            type: 'image/jpeg',
            lastModified: Date.now()
        });

        return finalFile;
    } catch (err) {
        console.error('Compression failed:', err);
        return file;
    }
};