@php
    $statusLabels = ['all' => 'Semua', 'active' => 'Aktif', 'inactive' => 'Nonaktif'];
    $sortLabels = [0 => ['Terbaru', 'desc'], 1 => ['Nama', 'asc']];
@endphp

<x-idx-toolbar placeholder="Cari nama / email ..." :status-labels="$statusLabels" default-status="all"
    :sort-labels="$sortLabels" :default-sort="[0, 'desc']" />

<table id="idxTable" class="table table-hover align-middle w-100">
    <thead><tr><th>ID</th><th>Nama</th><th>Email</th></tr></thead>
    <tbody></tbody>
</table>
<table id="idxTableMobile" class="table w-100 d-none">
    <thead><tr><th>Nama</th></tr></thead>
    <tbody></tbody>
</table>

@push('scripts')
<script>
    IdxMenu.init({
        dataUrl: '/dummy/users',
        defaultSort: [0, 'desc'],
        defaultStatus: 'all',
        mobileToServer: { 0: 1 },
        desktopColumns: [{ data: 'id' }, { data: 'name' }, { data: 'email' }],
        mobileColumns: [{ data: 'name' }],
    });
</script>
@endpush
