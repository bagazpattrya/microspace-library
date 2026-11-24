<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Microspace UI')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/docs.css') }}">
    <script>try { document.documentElement.dataset.bsTheme = localStorage.getItem('docs-theme') || 'light'; } catch (e) {}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <style>
        .side { width: 260px; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        @media (max-width: 767px) { .side { position: static; width: 100%; height: auto; } }
        pre { max-height: 360px; overflow: auto; }
    </style>
</head>
<body>
<div class="d-md-flex" x-data="{ q: '' }">
    <aside class="side border-end p-3 flex-shrink-0">
        <a href="/" class="brand"><span class="brand-mark">M</span>Microspace UI</a>
        <input x-model="q" class="form-control form-control-sm" placeholder="Cari komponen…" aria-label="Cari komponen">
        @foreach (config('catalog.categories') as $cat)
            <div class="nav-cat">{{ $cat }}</div>
            @foreach (collect(config('catalog.components'))->where('category', $cat) as $s => $item)
                <a href="/c/{{ $s }}" x-show="'{{ strtolower($item['title']) }}'.includes(q.toLowerCase())"
                   class="nav-item-l {{ request()->is("c/$s") ? 'active' : '' }}">{{ $item['title'] }}</a>
            @endforeach
        @endforeach
        <button class="theme-btn mt-4" x-data @click="const t = document.documentElement.dataset.bsTheme === 'dark' ? 'light' : 'dark'; document.documentElement.dataset.bsTheme = t; try { localStorage.setItem('docs-theme', t) } catch (e) {}">Ganti tema</button>
    </aside>
    <main class="flex-grow-1 p-4" style="min-width:0">@yield('content')</main>
</div>
</body>
</html>
