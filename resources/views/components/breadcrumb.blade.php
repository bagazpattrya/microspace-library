@php

$segments = Request::segments();

$isDashboard = Request::is('dashboard') || count($segments) === 0;

$currentRoute = request()->route();

@endphp


@if(!$isDashboard && count($segments) > 0)

<nav aria-label="breadcrumb">
    <ol class="breadcrumb align-items-center">
        <li class="breadcrumb-item">
            <a href="{{ url('/dashboard') }}"
                class="text-decoration-none"
>
                Dashboard
            </a>
        </li>
        @php
        $url = '';
        @endphp
        @foreach($segments as $index => $segment)
        @php
        $url .= '/' . $segment;

        if(
        request()->routeIs('units.show')
        &&
        $segment == request()->route('unit')->id
        ){
        $unit = request()->route('unit');
        $label =
        ($unit->cluster->name ?? 'Cluster')
        .' '
        .$unit->block_name
        .'-'
        .$unit->block_number;
        } else {

        $label = ucwords(str_replace(['-', '_'], ' ', $segment));
        }
        @endphp
        @if($segment !== 'dashboard')
        <li class="breadcrumb-item
                    {{ $loop->last ? 'active fw-bold' : '' }}"
>
            @if($loop->last)
            <span>
                {{ $label }}
            </span>
            @else
            <a href="{{ url($url) }}"
                class="text-decoration-none"
>
                {{ $label }}
            </a>
            @endif
        </li>
        @endif
        @endforeach
    </ol>
</nav>

@endif

<style>
    .breadcrumb-item a {
        color: var(--text-secondary, #6c757d);
        transition: color 0.2s;
    }

    .breadcrumb-item a:hover {
        color: var(--accent, #1e6fd9);
    }

    .breadcrumb-item.active,
    .breadcrumb-item.active span {
        color: var(--main-text, #212529);
    }

    .breadcrumb-item+.breadcrumb-item::before {
        color: var(--text-muted, #6c757d);
    }
</style>
