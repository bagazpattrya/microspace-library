<button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#detailDrawer">Buka Drawer</button>

<x-side-drawer id="detailDrawer" title="Detail" icon="bi-info-circle">
    <p>Isi drawer di sini.</p>

    <x-slot:footer>
        <button class="btn btn-primary w-100">Simpan</button>
    </x-slot:footer>
</x-side-drawer>
