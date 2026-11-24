@extends('docs.layout')
@section('content')
<h1 class="page-title">Komponen Microspace</h1>
<p class="lead-s mb-4">Pilih komponen, lihat preview, lalu salin kodenya.</p>
<div class="row g-3">
    @foreach ($components as $s => $c)
        <div class="col-md-4">
            <a href="/c/{{ $s }}" class="c-card">
                <span class="chip">{{ $c['category'] }}</span>
                <div class="t">{{ $c['title'] }}</div>
                <div class="s">{{ $c['summary'] }}</div>
            </a>
        </div>
    @endforeach
</div>
@endsection
