{{-- Word-like rich text editor (Quill 1.3.7). Usage: <x-rich-editor name="content" :value="$html" /> --}}
@props(['name' => 'content', 'value' => '', 'placeholder' => 'Tulis isi pengumuman...', 'height' => 360])

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

<div class="rich-editor-wrap" id="richEditorWrap-{{ $name }}">
    <div id="richEditor-{{ $name }}" style="min-height: {{ $height }}px;"></div>
    <input type="hidden" name="{{ $name }}" id="richEditorInput-{{ $name }}">
    <div class="text-danger small mt-1 d-none" id="richEditorError-{{ $name }}">Isi pengumuman wajib diisi.</div>
</div>

<style>
    .rich-editor-wrap .ql-toolbar.ql-snow {
        border: 1px solid var(--bs-border-color); border-radius: .5rem .5rem 0 0;
        background: var(--bs-tertiary-bg); font-family: inherit;
    }
    .rich-editor-wrap .ql-container.ql-snow {
        border: 1px solid var(--bs-border-color); border-top: 0; border-radius: 0 0 .5rem .5rem;
        background: var(--bs-body-bg); color: var(--bs-body-color); font-family: inherit; font-size: .95rem;
    }
    .rich-editor-wrap .ql-editor { min-height: {{ $height }}px; max-height: 60vh; overflow-y: auto; line-height: 1.7; }
    .rich-editor-wrap .ql-editor.ql-blank::before { color: var(--bs-secondary-color); font-style: normal; }
    .rich-editor-wrap:focus-within .ql-toolbar.ql-snow,
    .rich-editor-wrap:focus-within .ql-container.ql-snow { border-color: var(--golden-solid, #488700); }
    .rich-editor-wrap.is-invalid .ql-toolbar.ql-snow,
    .rich-editor-wrap.is-invalid .ql-container.ql-snow { border-color: var(--bs-danger); }
    /* toolbar icons follow the theme */
    .rich-editor-wrap .ql-snow .ql-stroke { stroke: var(--bs-body-color); }
    .rich-editor-wrap .ql-snow .ql-fill { fill: var(--bs-body-color); }
    .rich-editor-wrap .ql-snow .ql-picker { color: var(--bs-body-color); }
    .rich-editor-wrap .ql-snow .ql-picker-options { background: var(--bs-body-bg); border-color: var(--bs-border-color); }
    .rich-editor-wrap .ql-snow.ql-toolbar button:hover .ql-stroke,
    .rich-editor-wrap .ql-snow.ql-toolbar button.ql-active .ql-stroke,
    .rich-editor-wrap .ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke { stroke: var(--golden-solid, #488700); }
    .rich-editor-wrap .ql-snow.ql-toolbar button:hover .ql-fill,
    .rich-editor-wrap .ql-snow.ql-toolbar button.ql-active .ql-fill { fill: var(--golden-solid, #488700); }
    .rich-editor-wrap .ql-snow.ql-toolbar button.ql-active,
    .rich-editor-wrap .ql-snow.ql-toolbar .ql-picker-label:hover,
    .rich-editor-wrap .ql-snow.ql-toolbar .ql-picker-label.ql-active { color: var(--golden-solid, #488700); }
    .rich-editor-wrap .ql-tooltip { background: var(--bs-body-bg); color: var(--bs-body-color); border-color: var(--bs-border-color); z-index: 5; }
    .rich-editor-wrap .ql-tooltip input[type=text] { background: var(--bs-body-bg); color: var(--bs-body-color); border-color: var(--bs-border-color); }
</style>

<script>
    (function () {
        const name = @json($name);
        const initial = @json((string) $value);
        const input = document.getElementById('richEditorInput-' + name);
        const wrap = document.getElementById('richEditorWrap-' + name);
        const err = document.getElementById('richEditorError-' + name);

        const quill = new Quill('#richEditor-' + name, {
            theme: 'snow',
            placeholder: @json($placeholder),
            // whitelist: pasted color, background, font, size, etc. are dropped
            formats: ['header', 'bold', 'italic', 'underline', 'strike', 'list', 'indent', 'align', 'blockquote', 'link'],
            modules: {
                toolbar: [
                    [{ header: [2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
                    [{ align: [] }],
                    ['blockquote', 'link'],
                    ['clean'],
                ],
            },
        });

        if (initial) quill.clipboard.dangerouslyPasteHTML(initial);

        const isEmpty = () => quill.getText().trim().length === 0;
        const sync = () => { input.value = isEmpty() ? '' : quill.root.innerHTML; };
        sync();
        quill.on('text-change', () => {
            sync();
            if (!isEmpty()) { wrap.classList.remove('is-invalid'); err.classList.add('d-none'); }
        });

        input.form.addEventListener('submit', function (e) {
            sync();
            if (isEmpty()) {
                e.preventDefault();
                wrap.classList.add('is-invalid');
                err.classList.remove('d-none');
                quill.focus();
            }
        });
    })();
</script>
