<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link id="theme-stylesheet" rel="stylesheet" href="{{ asset('css/microspace/dark-theme.css') }}">
    @foreach (['style', 'style-mobile', 'cards', 'button', 'skeleton', 'form-section', 'alertx', 'auth', 'index-menu'] as $css)
        <link rel="stylesheet" href="{{ asset("css/microspace/$css.css") }}">
    @endforeach
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script src="{{ asset('js/microspace/skeleton.js') }}"></script>
    <script src="{{ asset('js/microspace/index-menu.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/docs-preview.css') }}">
    @stack('styles')
    <style>body, table.dataTable, .dataTables_wrapper { font-family: "Inter", system-ui, sans-serif; } * { scrollbar-width: none; } *::-webkit-scrollbar { display: none; } body { padding: 1.5rem; min-height: 100vh; }</style>
</head>
<body>
    @include('examples.' . $slug)
    @stack('scripts')
    <script>
        // Tema dari parent (halaman docs) lewat postMessage.
        addEventListener('message', e => {
            if (!['light', 'dark'].includes(e.data?.theme)) return;
            document.documentElement.dataset.bsTheme = e.data.theme;
            document.getElementById('theme-stylesheet').href = "{{ asset('css/microspace') }}/" + e.data.theme + '-theme.css';
        });
    </script>
</body>
</html>
