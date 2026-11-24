{{--
    Flash alert toast — same markup/look as the main layout's alert
    (styles: public/css/microspace/alertx.css).

    Shows, in the same way as the main layout:
      - session('success')            -> green toast
      - session('error') / $errors    -> red toast (list of validation errors)

    Auto-dismisses after 5s (matches the CSS countdown bar) or on the close button.
    Include once per page, after alertx.css is loaded.
--}}
@php
    // $errors is shared by the web middleware; fall back to an empty bag if this renders without it.
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp
@if (session('success'))
    <div class="alertx-container js-flash-alert" role="status" aria-live="polite">
        <div class="alertx">
            {{ session('success') }}
            <span class="close-btn" role="button" aria-label="Tutup">&times;</span>
        </div>
    </div>
@endif

@if (session('error') || $errors->any())
    <div class="alertx-container js-flash-alert" role="alert">
        <div class="alertx alertx-danger">
            @if (session('error'))
                {{ session('error') }}
            @endif
            @if ($errors->any())
                <ul class="mb-0" style="list-style: none; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <span class="close-btn" role="button" aria-label="Tutup">&times;</span>
        </div>
    </div>
@endif

@if (session('success') || session('error') || $errors->any())
    <script>
        (function () {
            document.querySelectorAll('.js-flash-alert').forEach(function (box) {
                var closed = false;
                function close() {
                    if (closed) return;
                    closed = true;
                    box.style.transition = 'opacity .4s ease';
                    box.style.opacity = '0';
                    setTimeout(function () { box.remove(); }, 400);
                }
                var btn = box.querySelector('.close-btn');
                if (btn) btn.addEventListener('click', close);
                setTimeout(close, 5000);
            });
        })();
    </script>
@endif
