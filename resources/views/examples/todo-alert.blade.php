@php
    $todos = [
        ['type' => 'danger', 'icon' => 'receipt_long', 'title' => '3 Invoice Jatuh Tempo', 'message' => 'Segera lakukan penagihan.', 'count' => 3, 'action_url' => '/invoices'],
        ['type' => 'warning', 'icon' => 'schedule', 'title' => 'Absensi belum lengkap', 'message' => 'Ada 2 karyawan belum check-out.', 'count' => 2, 'action_url' => '/attendance'],
        ['type' => 'info', 'icon' => 'campaign', 'title' => 'Pengumuman baru', 'message' => 'Jadwal libur bersama telah terbit.', 'action_url' => '/announcements'],
    ];
@endphp

<x-todo-alert :todos="$todos" />
