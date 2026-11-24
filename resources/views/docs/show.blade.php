@extends('docs.layout')
@section('title', $c['title'].' · Microspace UI')
@section('content')
<h1 class="page-title">{{ $c['title'] }}</h1>
<p class="lead-s">{{ $c['summary'] }}</p>
<div class="hint mb-4"><strong>Kapan dipakai:</strong> {{ $c['use_when'] }}</div>

<div x-data="{ theme: 'dark', width: '100%', copied: false }">
    <div class="d-flex gap-2 mb-2 flex-wrap">
        <div class="btn-group btn-group-sm seg">
            <button class="btn btn-outline-secondary" :class="theme==='light' && 'active'" @click="theme='light'; $refs.f.contentWindow.postMessage({theme}, '*')">Light</button>
            <button class="btn btn-outline-secondary" :class="theme==='dark' && 'active'" @click="theme='dark'; $refs.f.contentWindow.postMessage({theme}, '*')">Dark</button>
        </div>
        <div class="btn-group btn-group-sm seg">
            <button class="btn btn-outline-secondary" :class="width==='100%' && 'active'" @click="width='100%'">Desktop</button>
            <button class="btn btn-outline-secondary" :class="width==='390px' && 'active'" @click="width='390px'">Mobile</button>
        </div>
    </div>
    <div class="stage">
        <div class="device" :class="width==='390px' ? 'phone' : 'laptop'">
            <div class="lid">
                <i class="cam"></i><i class="island"></i>
                <div class="screen">
                    <div class="status"><span>12:05</span><span>5G ▮▮▮</span></div>
                    <div class="urlbar"><span>microspace.test</span></div>
                    <iframe x-ref="f" src="/preview/{{ $slug }}"></iframe>
                    <div class="tabbar"><span>microspace.test</span></div>
                </div>
            </div>
            <div class="base"><span>MacBook Air</span></div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 mb-1">
        <h2 class="h5 m-0">Kode</h2>
        <button class="btn btn-sm btn-primary" @click="navigator.clipboard.writeText($refs.code.innerText); copied=true; setTimeout(()=>copied=false,1500)" x-text="copied ? 'Tersalin ✓' : 'Copy'"></button>
    </div>
    <pre class="code"><code x-ref="code">{{ $code }}</code></pre>
</div>

<h2 class="h5 mt-4">File yang dibawa ke project</h2>
<ul>@foreach ($c['files'] as $f)<li><code>{{ $f }}</code></li>@endforeach</ul>

@if ($c['props'])
<h2 class="h5 mt-4">Props</h2>
<table class="table table-sm">
    <thead><tr><th>Nama</th><th>Tipe</th><th>Default</th><th>Keterangan</th></tr></thead>
    <tbody>@foreach ($c['props'] as $p)<tr><td><code>{{ $p[0] }}</code></td><td>{{ $p[1] }}</td><td><code>{{ $p[2] }}</code></td><td>{{ $p[3] }}</td></tr>@endforeach</tbody>
</table>
@endif
@endsection
