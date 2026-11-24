<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

$find = fn (string $slug) => config("catalog.components.$slug") ?? abort(404);

Route::get('/', fn () => view('docs.index', ['components' => config('catalog.components')]));

Route::get('/c/{slug}', function (string $slug) use ($find) {
    return view('docs.show', [
        'slug' => $slug,
        'c' => $find($slug),
        'code' => File::get(resource_path("views/examples/$slug.blade.php")),
        'components' => config('catalog.components'),
    ]);
});

Route::get('/preview/{slug}', function (string $slug) use ($find) {
    $find($slug);
    session()->now('success', 'Data berhasil disimpan.'); // demo untuk flash-alert

    return view('docs.preview', ['slug' => $slug]);
});

// Data dummy untuk demo idx-toolbar (format server-side DataTables).
Route::get('/dummy/users', function (\Illuminate\Http\Request $r) {
    $rows = collect(range(1, 40))->map(fn ($i) => [
        'id' => $i,
        'name' => ['Andi', 'Budi', 'Citra', 'Dewi', 'Eka'][$i % 5]." $i",
        'email' => 'user'.$i.'@contoh.id',
        'is_active' => $i % 3 !== 0,
    ]);
    $status = $r->query('status', 'all');
    if ($status !== 'all') {
        $rows = $rows->where('is_active', $status === 'active');
    }
    if ($q = strtolower($r->input('search.value', $r->query('search', '')))) {
        $rows = $rows->filter(fn ($x) => str_contains(strtolower($x['name'].$x['email']), $q));
    }
    $col = [0 => 'id', 1 => 'name', 2 => 'email'][(int) $r->input('order.0.column', 0)] ?? 'id';
    $rows = $rows->sortBy($col, SORT_REGULAR, $r->input('order.0.dir') === 'desc')->values();

    return [
        'draw' => (int) $r->input('draw'),
        'recordsTotal' => 40,
        'recordsFiltered' => $rows->count(),
        'data' => $rows->slice((int) $r->input('start', 0), (int) $r->input('length', 10))->values(),
    ];
});
